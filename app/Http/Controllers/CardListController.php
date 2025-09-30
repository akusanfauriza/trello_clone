<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\CardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardListController extends Controller
{
    public function store(Request $request, $boardId)
    {
        $board = Board::findOrFail($boardId);
        
        // Authorization check
        if ($board->user_id !== Auth::id() && !$board->members()->where('user_id', Auth::id())->where('role', 'admin')->exists()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $position = $board->lists()->max('position') + 1;

        CardList::create([
            'name' => $request->name,
            'board_id' => $board->id,
            'position' => $position
        ]);

        return back()->with('success', 'List created successfully!');
    }

    public function update(Request $request, $id)
    {
        $list = CardList::with('board')->findOrFail($id);
        
        if ($list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $list->update(['name' => $request->name]);

        return back()->with('success', 'List updated successfully!');
    }

    public function destroy($id)
    {
        $list = CardList::with('board')->findOrFail($id);
        
        if ($list->board->user_id !== Auth::id()) {
            abort(403);
        }

        $list->delete();

        return back()->with('success', 'List deleted successfully!');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'lists' => 'required|array',
            'lists.*.id' => 'required|exists:lists,id',
            'lists.*.position' => 'required|integer'
        ]);

        foreach ($request->lists as $listData) {
            CardList::where('id', $listData['id'])->update(['position' => $listData['position']]);
        }

        return response()->json(['message' => 'Lists reordered successfully']);
    }
}