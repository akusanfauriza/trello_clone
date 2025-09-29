<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'color',
        'user_id',
        'is_public'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lists()
    {
        return $this->hasMany(CardList::class)->orderBy('position');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'board_members')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}