<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'genre',
        'description',
        'release_year',
        'cover_image',
        'video_path',
        'created_by',
    ];

    /**
     * Get the user who created this movie
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get comments for this movie
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get ratings for this movie
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get average rating for this movie
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get total ratings count for this movie
     */
    public function getRatingsCountAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Get watchlist items for this movie
     */
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get users who have this movie in their watchlist
     */
    public function watchlistUsers()
    {
        return $this->belongsToMany(User::class, 'watchlists')
                    ->wherePivot('type', 'watchlist')
                    ->withTimestamps();
    }

    /**
     * Get users who have favorited this movie
     */
    public function favoritedByUsers()
    {
        return $this->belongsToMany(User::class, 'watchlists')
                    ->wherePivot('type', 'favorite')
                    ->withTimestamps();
    }

    /**
     * Get tags for this movie
     */
    public function tags()
    {
        return $this->hasMany(MovieTag::class);
    }

    /**
     * Scope to filter by genre
     */
    public function scopeByGenre($query, $genre)
    {
        return $query->where('genre', 'like', '%' . $genre . '%');
    }

    /**
     * Scope to filter by release year
     */
    public function scopeByYear($query, $year)
    {
        return $query->where('release_year', $year);
    }

    /**
     * Scope to filter by minimum rating
     */
    public function scopeByMinRating($query, $minRating)
    {
        return $query->withAvg('ratings', 'rating')
                    ->having('ratings_avg_rating', '>=', $minRating);
    }

    /**
     * Scope to search by title
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    /**
     * Scope to filter by tag
     */
    public function scopeByTag($query, $tag)
    {
        return $query->whereHas('tags', function ($q) use ($tag) {
            $q->where('tag', $tag);
        });
    }
}
