<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\CardList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListController extends Controller
{
    public function store(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $position = $board->lists()->max('position') + 1;

        $list = CardList::create([
            'name' => $request->name,
            'board_id' => $board->id,
            'position' => $position
        ]);

        return response()->json($list, 201);
    }

    public function update(Request $request, CardList $list)
    {
        $this->authorize('update', $list->board);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|integer'
        ]);

        $list->update($request->only(['name', 'position']));

        return response()->json($list);
    }

    public function destroy(CardList $list)
    {
        $this->authorize('update', $list->board);

        $list->delete();

        // Reorder remaining lists
        $board = $list->board;
        $lists = $board->lists()->orderBy('position')->get();
        
        foreach ($lists as $index => $listItem) {
            $listItem->update(['position' => $index]);
        }

        return response()->json(['message' => 'List deleted successfully']);
    }

    public function reorder(Request $request, Board $board)
    {
        $this->authorize('update', $board);

        $request->validate([
            'lists' => 'required|array',
            'lists.*.id' => 'required|exists:lists,id',
            'lists.*.position' => 'required|integer'
        ]);

        foreach ($request->lists as $listData) {
            CardList::where('id', $listData['id'])
                    ->where('board_id', $board->id)
                    ->update(['position' => $listData['position']]);
        }

        return response()->json(['message' => 'Lists reordered successfully']);
    }
}