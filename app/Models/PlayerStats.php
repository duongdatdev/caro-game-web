<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerStats extends Model
{
    protected $fillable = [
        'user_id',
        'wins',
        'losses', 
        'draws',
        'rating'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updateRating($isWinner)
    {
        $ratingChange = $isWinner ? 15 : -15;
        $this->rating += $ratingChange;
        $this->save();
        return $ratingChange;
    }
}