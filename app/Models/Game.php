<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    protected $fillable = [
        'room_id',
        'status',
        'winner_id',
        'board_state'
    ];

    protected $casts = [
        'board_state' => 'array'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }

    public function players()
    {
        return $this->belongsToMany(
            User::class,
            'room_players',  // pivot table
            'room_id',       // foreign key
            'user_id'        // related key
        )->using('App\Models\RoomPlayer');
    }
}