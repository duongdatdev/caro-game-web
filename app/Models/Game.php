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
