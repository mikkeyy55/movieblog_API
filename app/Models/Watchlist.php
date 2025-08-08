<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watchlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'movie_id',
        'type',
    ];

    /**
     * Get the user that owns the watchlist item
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie associated with the watchlist item
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Scope to filter by type (watchlist or favorite)
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}