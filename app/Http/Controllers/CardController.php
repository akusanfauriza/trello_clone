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
        $this->authorize('update', $list->board);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $position = $list->cards()->max('position') + 1;

        $card = Card::create([
            'title' => $request->title,
            'description' => $request->description,
            'position' => $position,
            'list_id' => $list->id,
            'user_id' => Auth::id()
        ]);

        return response()->json($card->load(['user', 'members']), 201);
    }

    public function show(Card $card)
    {
        $this->authorize('view', $card->board);

        $card->load([
            'user',
            'members',
            'list.board',
            'activities' => function($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }
        ]);

        return response()->json($card);
    }

    public function update(Request $request, Card $card)
    {
        $this->authorize('update', $card->board);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'position' => 'sometimes|integer',
            'list_id' => 'sometimes|exists:lists,id'
        ]);

        $oldValues = $card->toArray();

        $card->update($request->only(['title', 'description', 'due_date', 'position', 'list_id']));

        // Log activity
        if ($card->wasChanged()) {
            ActivityController::logCardUpdate($card, $oldValues);
        }

        return response()->json($card->load(['user', 'members']));
    }

    public function destroy(Card $card)
    {
        $this->authorize('update', $card->board);

        $card->delete();

        // Reorder remaining cards in the list
        $list = $card->list;
        $cards = $list->cards()->orderBy('position')->get();
        
        foreach ($cards as $index => $cardItem) {
            $cardItem->update(['position' => $index]);
        }

        return response()->json(['message' => 'Card deleted successfully']);
    }

    public function addMember(Request $request, Card $card)
    {
        $this->authorize('update', $card->board);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        if (!$card->board->isMember($request->user_id)) {
            return response()->json(['message' => 'User is not a board member'], 422);
        }

        $card->members()->syncWithoutDetaching([$request->user_id]);

        ActivityController::logCardMemberAdd($card, $request->user_id);

        return response()->json(['message' => 'Member added to card successfully']);
    }

    public function removeMember(Request $request, Card $card, $userId)
    {
        $this->authorize('update', $card->board);

        $card->members()->detach($userId);

        ActivityController::logCardMemberRemove($card, $userId);

        return response()->json(['message' => 'Member removed from card successfully']);
    }
}