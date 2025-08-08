<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        // Get statistics
        $stats = [
            'total_movies' => Movie::count(),
            'total_users' => User::count(),
            'total_comments' => Comment::count(),
            'total_ratings' => Rating::count(),
            'recent_movies' => Movie::latest()->take(5)->get(),
            'recent_users' => User::latest()->take(5)->get(),
            'top_rated_movies' => Movie::withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->having('ratings_count', '>=', 1)
                ->orderByDesc('ratings_avg_rating')
                ->take(5)
                ->get(),
            'most_commented_movies' => Movie::withCount('comments')
                ->orderByDesc('comments_count')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show all movies with admin controls
     */
    public function movies()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        $movies = Movie::with(['creator', 'comments', 'ratings'])
            ->withAvg('ratings', 'rating')
            ->withCount(['ratings', 'comments'])
            ->latest()
            ->paginate(15);

        return view('admin.movies', compact('movies'));
    }

    /**
     * Show all users
     */
    public function users()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        $users = User::withCount(['comments', 'ratings'])
            ->latest()
            ->paginate(15);

        return view('admin.users', compact('users'));
    }

    /**
     * Show system statistics
     */
    public function statistics()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        // Monthly statistics
        $monthlyStats = DB::table('movies')
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Genre distribution
        $genreStats = DB::table('movies')
            ->selectRaw('genre, COUNT(*) as count')
            ->groupBy('genre')
            ->orderByDesc('count')
            ->get();

        // Rating distribution
        $ratingStats = DB::table('ratings')
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating')
            ->get();

        $stats = [
            'monthly_stats' => $monthlyStats,
            'genre_stats' => $genreStats,
            'rating_stats' => $ratingStats,
        ];

        return view('admin.statistics', compact('stats'));
    }

    /**
     * Delete a movie (admin only)
     */
    public function deleteMovie($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        try {
            $movie = Movie::findOrFail($id);
            $movieTitle = $movie->title;
            $movie->delete();

            return redirect()->route('admin.movies')
                ->with('success', "Movie '$movieTitle' deleted successfully!");

        } catch (\Exception $e) {
            return redirect()->route('admin.movies')
                ->with('error', 'Failed to delete movie. Please try again.');
        }
    }

    /**
     * Delete a user (admin only)
     */
    public function deleteUser($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        // Prevent admin from deleting themselves
        if ($id == Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        try {
            $user = User::findOrFail($id);
            $userName = $user->name;
            $user->delete();

            return redirect()->route('admin.users')
                ->with('success', "User '$userName' deleted successfully!");

        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Toggle user admin status
     */
    public function toggleAdmin($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        // Prevent admin from changing their own status
        if ($id == Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot change your own admin status.');
        }

        try {
            $user = User::findOrFail($id);
            $user->role = $user->role === 'admin' ? 'user' : 'admin';
            $user->save();

            $status = $user->role === 'admin' ? 'promoted to admin' : 'demoted to user';
            return redirect()->route('admin.users')
                ->with('success', "User '$user->name' $status successfully!");

        } catch (\Exception $e) {
            return redirect()->route('admin.users')
                ->with('error', 'Failed to update user status. Please try again.');
        }
    }
}
