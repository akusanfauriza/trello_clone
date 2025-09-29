<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Board
    public function boards()
    {
        return $this->hasMany(Board::class);
    }

    public function memberBoards()
    {
        return $this->belongsToMany(Board::class, 'board_members');
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function cardMemberships()
    {
        return $this->belongsToMany(Card::class, 'card_members');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
}
}
