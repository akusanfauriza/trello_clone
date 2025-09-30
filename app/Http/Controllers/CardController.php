<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\CardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function store(Request $request, CardList $list)
    {
        // Authorization check - PERBAIKAN: Gunakan policy atau manual check
        if ($list->board->user_id !== Auth::id() && !$list->board->isMember(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $position = $list->cards()->max('position') + 1;

        $card = Card::create([
            'title' => $request->title,
            'description' => $request->description,
            'position' => $position,
            'card_list_id' => $list->id,
            'user_id' => Auth::id()
        ]);

        // Redirect back to board page instead of returning JSON
        return redirect()->route('boards.show', $list->board->id)
            ->with('success', 'Card created successfully!');
    }

    public function show(Card $card)
    {
        // Authorization check
        if (!$card->list->board->is_public && 
            $card->list->board->user_id !== Auth::id() && 
            !$card->list->board->isMember(Auth::id())) {
            abort(403);
        }

        $card->load(['user', 'members', 'list.board']);

        // Return JSON for AJAX requests, or view for normal requests
        if (request()->wantsJson()) {
            return response()->json($card);
        }

        // For normal requests, redirect to board
        return redirect()->route('boards.show', $card->list->board->id);
    }

    public function update(Request $request, Card $card)
    {
        // Authorization check
        if ($card->list->board->user_id !== Auth::id() && !$card->list->board->isMember(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'position' => 'sometimes|integer',
            'list_id' => 'sometimes|exists:lists,id'
        ]);

        $card->update($request->only(['title', 'description', 'due_date', 'position', 'list_id']));

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'card' => $card->load(['user', 'members'])]);
        }

        return back()->with('success', 'Card updated successfully!');
    }

    public function destroy(Card $card)
    {
        if ($card->list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $boardId = $card->list->board->id;
        $card->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Card deleted successfully']);
        }

        return redirect()->route('boards.show', $boardId)->with('success', 'Card deleted successfully!');
    }

    public function move(Request $request, Card $card)
    {
        $request->validate([
            'list_id' => 'required|exists:lists,id',
            'position' => 'required|integer'
        ]);

        $card->update([
            'list_id' => $request->list_id,
            'position' => $request->position
        ]);

        return response()->json(['success' => true]);
    }

    public function addMember(Request $request, Card $card)
    {
        // Authorization check
        if ($card->list->board->user_id !== Auth::id() && !$card->list->board->isMember(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$card->list->board->isMember($request->user_id)) {
            return back()->with('error', 'User is not a member of this board.');
        }

        $card->members()->syncWithoutDetaching([$request->user_id]);

        return back()->with('success', 'Member added to card successfully!');
    }

    public function removeMember(Card $card, $userId)
    {
        // Authorization check
        if ($card->list->board->user_id !== Auth::id() && !$card->list->board->isMember(Auth::id())) {
            abort(403);
        }

        $card->members()->detach($userId);

        return back()->with('success', 'Member removed from card successfully!');
    }
}