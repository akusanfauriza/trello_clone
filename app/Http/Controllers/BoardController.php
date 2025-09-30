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
        
        $ownedBoards = $user->boards()->with('user')->get();
        $memberBoards = $user->memberBoards()->with('user')->get();

        return view('boards.index', compact('ownedBoards', 'memberBoards'));
    }

    public function create()
    {
        return view('boards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7'
        ]);

        $board = Board::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color ?? '#0079bf',
            'user_id' => Auth::id()
        ]);

        // Add creator as admin member
        $board->members()->attach(Auth::id(), ['role' => 'admin']);

        // PERBAIKAN: Redirect ke board show page, bukan return JSON
        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Board created successfully!');
    }

    public function show(Board $board)
    {
        // Authorization check
        if (!$board->is_public && $board->user_id !== Auth::id() && !$board->isMember(Auth::id())) {
            abort(403, 'Unauthorized access to this board.');
        }

        $board->load([
            'user',
            'members',
            'lists' => function($query) {
                $query->orderBy('position')->with(['cards' => function($q) {
                    $q->orderBy('position')->with(['user', 'members']);
                }]);
            },
            'activities' => function($query) {
                $query->orderBy('created_at', 'desc')->with('user');
            }
        ]);

        return view('boards.show', compact('board'));
    }

    public function edit(Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }

        return view('boards.edit', compact('board'));
    }

    public function update(Request $request, Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7'
        ]);

        $board->update($request->only(['name', 'description', 'color']));

        // PERBAIKAN: Redirect back dengan success message
        return redirect()->route('boards.show', $board->id)
            ->with('success', 'Board updated successfully!');
    }

    public function destroy(Board $board)
    {
        if ($board->user_id !== Auth::id()) {
            abort(403);
        }

        $board->delete();

        return redirect()->route('dashboard')->with('success', 'Board deleted successfully!');
    }
}