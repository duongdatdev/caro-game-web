<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Game extends Model
{
    use HasFactory;
    protected $fillable = [
        'room_id',
        'status',
        'current_player'
    ];

    protected $casts = [
        'board_state' => 'array'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }

    public function currentPlayer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_player');
    }

    /*
    * Helper method to initialize the game with creator as first player
    */
    public static function initializeGame(Room $room): self
    {
        return self::create([
            'room_id' => $room->id,
            'status' => 'playing',
            'current_player' => $room->created_by 
        ]);
    }

    /*
    * Helper method to switch turns
    */
    public function switchTurn(): void
    {
        // Get the other player in the room
        $nextPlayer = $this->room->players()
            ->where('user_id', '!=', $this->current_player)
            ->first();

        if ($nextPlayer) {
            $this->current_player = $nextPlayer->id;
            $this->save();
        }
    }

    public function players()
    {
        // Get players through the room relationship
        return $this->hasManyThrough(
            User::class,
            Room::class,
            'id', // Foreign key on rooms table
            'id', // Foreign key on users table
            'room_id', // Local key on games table
            'created_by' // Local key on rooms table
        );
    }
}
