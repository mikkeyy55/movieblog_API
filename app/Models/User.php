<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get user's comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get user's ratings
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get movies created by this user (if admin)
     */
    public function movies()
    {
        return $this->hasMany(Movie::class, 'created_by');
    }

    /**
     * Get user's watchlist items
     */
    public function watchlists()
    {
        return $this->hasMany(Watchlist::class);
    }

    /**
     * Get user's favorite movies
     */
    public function favoriteMovies()
    {
        return $this->belongsToMany(Movie::class, 'watchlists')
                    ->wherePivot('type', 'favorite')
                    ->withTimestamps();
    }

    /**
     * Get user's watchlist movies
     */
    public function watchlistMovies()
    {
        return $this->belongsToMany(Movie::class, 'watchlists')
                    ->wherePivot('type', 'watchlist')
                    ->withTimestamps();
    }
}
