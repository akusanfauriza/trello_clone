<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'position',
        'due_date',
        'list_id',
        'user_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function list()
    {
        return $this->belongsTo(CardList::class, 'list_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'card_members')
                    ->withTimestamps();
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function board()
    {
        return $this->hasOneThrough(Board::class, CardList::class, 'id', 'id', 'list_id', 'board_id');
    }
}