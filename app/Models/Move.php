<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Move extends Model
{
    protected $table = 'game_moves';
    protected $fillable = [
        'user_id',
        'x',
        'y',
        'order'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}