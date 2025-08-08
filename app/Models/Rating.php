<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
    ];

    /**
     * Get the user who made this rating
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie this rating belongs to
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
