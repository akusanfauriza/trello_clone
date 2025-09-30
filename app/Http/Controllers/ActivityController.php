<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public static function logCardUpdate(Card $card, array $oldValues)
    {
        $newValues = $card->toArray();
        $changes = [];

        foreach ($oldValues as $key => $oldValue) {
            if (isset($newValues[$key]) && $oldValue != $newValues[$key] && in_array($key, ['title', 'description', 'due_date', 'list_id'])) {
                $changes[$key] = [
                    'from' => $oldValue,
                    'to' => $newValues[$key]
                ];
            }
        }

        if (!empty($changes)) {
            Activity::create([
                'type' => 'card_updated',
                'description' => 'updated card',
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'user_id' => Auth::id(),
                'board_id' => $card->board->id,
                'card_id' => $card->id
            ]);
        }
    }

    public static function logCardMemberAdd(Card $card, $userId)
    {
        $user = User::find($userId);

        Activity::create([
            'type' => 'card_member_added',
            'description' => "added {$user->name} to card",
            'user_id' => Auth::id(),
            'board_id' => $card->board->id,
            'card_id' => $card->id
        ]);
    }

    public static function logCardMemberRemove(Card $card, $userId)
    {
        $user = User::find($userId);

        Activity::create([
            'type' => 'card_member_removed',
            'description' => "removed {$user->name} from card",
            'user_id' => Auth::id(),
            'board_id' => $card->board->id,
            'card_id' => $card->id
        ]);
    }

    public static function logCardCreated(Card $card)
    {
        Activity::create([
            'type' => 'card_created',
            'description' => "created card \"{$card->title}\"",
            'user_id' => Auth::id(),
            'board_id' => $card->board->id,
            'card_id' => $card->id
        ]);
    }
}