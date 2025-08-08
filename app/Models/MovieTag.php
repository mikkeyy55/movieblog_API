<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'movie_id',
        'tag',
    ];

    /**
     * Get the movie that owns the tag
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * Scope to filter by tag
     */
    public function scopeForTag($query, $tag)
    {
        return $query->where('tag', $tag);
    }
}