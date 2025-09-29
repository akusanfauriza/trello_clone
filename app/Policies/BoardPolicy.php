<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    public function view(User $user, Board $board): bool
    {
        return $board->is_public || 
               $board->user_id === $user->id || 
               $board->isMember($user->id);
    }

    public function update(User $user, Board $board): bool
    {
        return $board->user_id === $user->id || 
               $board->members()->where('user_id', $user->id)->where('role', 'admin')->exists();
    }

    public function delete(User $user, Board $board): bool
    {
        return $board->user_id === $user->id;
    }
}