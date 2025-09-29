<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $ownedBoards = $user->boards()
            ->with(['user', 'members'])
            ->get();

        $memberBoards = $user->memberBoards()
            ->with(['user', 'members'])
            ->get();

        return response()->json([
            'owned_boards' => $ownedBoards,
            'member_boards' => $memberBoards
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_public' => 'boolean'
        ]);

        $board = Board::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#0079bf',
            'is_public' => $request->is_public ?? false,
            'user_id' => Auth::id()
        ]);

        // Add creator as admin member
        $board->members()->attach(Auth::id(), ['role' => 'admin']);

        return response()->json($board->load(['user', 'members']), 201);
    }

    public function show(Board $board)
    {
        $this->authorize('view', $board);

        $board->load([
            'user',
            'members',
            'lists.cards' => function($query) {
                $query->orderBy('position')->with(['user', 'members']);
            },
            'activities' => function($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }
        ]);

        return response()->json($board);
    }

    public function update(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_public' => 'boolean'
        ]);

        $board->update($request->only(['name', 'description', 'color', 'is_public']));

        return response()->json($board->load(['user', 'members']));
    }

    public function destroy(Board $board)
    {
        $this->authorize('delete', $board);

        $board->delete();

        return response()->json(['message' => 'Board deleted successfully']);
    }

    public function addMember(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($board->isMember($user->id)) {
            return response()->json(['message' => 'User is already a member'], 422);
        }

        $board->members()->attach($user->id, ['role' => 'member']);

        return response()->json(['message' => 'Member added successfully']);
    }

    public function removeMember(Request $request, Board $board, User $user)
    {
        $this->authorize('update', $board);

        if ($board->user_id === $user->id) {
            return response()->json(['message' => 'Cannot remove board owner'], 422);
        }

        $board->members()->detach($user->id);

        return response()->json(['message' => 'Member removed successfully']);
    }
}