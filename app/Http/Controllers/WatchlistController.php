<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WatchlistController extends Controller
{
    /**
     * Get user's watchlist
     */
    public function getWatchlist(Request $request): JsonResponse
    {
        $watchlist = Watchlist::with(['movie.creator', 'movie.ratings'])
            ->where('user_id', auth()->id())
            ->where('type', 'watchlist')
            ->get();

        return response()->json([
            'watchlist' => $watchlist
        ]);
    }

    /**
     * Get user's favorites
     */
    public function getFavorites(Request $request): JsonResponse
    {
        $favorites = Watchlist::with(['movie.creator', 'movie.ratings'])
            ->where('user_id', auth()->id())
            ->where('type', 'favorite')
            ->get();

        return response()->json([
            'favorites' => $favorites
        ]);
    }

    /**
     * Add movie to watchlist
     */
    public function addToWatchlist(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $movie = Movie::findOrFail($request->movie_id);

        // Check if already in watchlist
        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'watchlist')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Movie already in watchlist'
            ], 409);
        }

        $watchlistItem = Watchlist::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'type' => 'watchlist',
        ]);

        return response()->json([
            'message' => 'Movie added to watchlist successfully',
            'watchlist_item' => $watchlistItem->load('movie')
        ], 201);
    }

    /**
     * Add movie to favorites
     */
    public function addToFavorites(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $movie = Movie::findOrFail($request->movie_id);

        // Check if already in favorites
        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'favorite')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Movie already in favorites'
            ], 409);
        }

        $favoriteItem = Watchlist::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'type' => 'favorite',
        ]);

        return response()->json([
            'message' => 'Movie added to favorites successfully',
            'favorite_item' => $favoriteItem->load('movie')
        ], 201);
    }

    /**
     * Remove movie from watchlist
     */
    public function removeFromWatchlist(Request $request, $movieId): JsonResponse
    {
        $watchlistItem = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $movieId)
            ->where('type', 'watchlist')
            ->first();

        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Movie not found in watchlist'
            ], 404);
        }

        $watchlistItem->delete();

        return response()->json([
            'message' => 'Movie removed from watchlist successfully'
        ]);
    }

    /**
     * Remove movie from favorites
     */
    public function removeFromFavorites(Request $request, $movieId): JsonResponse
    {
        $favoriteItem = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $movieId)
            ->where('type', 'favorite')
            ->first();

        if (!$favoriteItem) {
            return response()->json([
                'message' => 'Movie not found in favorites'
            ], 404);
        }

        $favoriteItem->delete();

        return response()->json([
            'message' => 'Movie removed from favorites successfully'
        ]);
    }

    /**
     * Toggle movie in watchlist
     */
    public function toggleWatchlist(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'watchlist')
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Movie removed from watchlist',
                'in_watchlist' => false
            ]);
        } else {
            $watchlistItem = Watchlist::create([
                'user_id' => auth()->id(),
                'movie_id' => $request->movie_id,
                'type' => 'watchlist',
            ]);

            return response()->json([
                'message' => 'Movie added to watchlist',
                'in_watchlist' => true,
                'watchlist_item' => $watchlistItem->load('movie')
            ]);
        }
    }

    /**
     * Toggle movie in favorites
     */
    public function toggleFavorite(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'favorite')
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Movie removed from favorites',
                'is_favorite' => false
            ]);
        } else {
            $favoriteItem = Watchlist::create([
                'user_id' => auth()->id(),
                'movie_id' => $request->movie_id,
                'type' => 'favorite',
            ]);

            return response()->json([
                'message' => 'Movie added to favorites',
                'is_favorite' => true,
                'favorite_item' => $favoriteItem->load('movie')
            ]);
        }
    }

    /**
     * Toggle movie in watchlist for web interface
     */
    public function toggleWatchlistWeb(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'watchlist')
            ->first();

        if ($existing) {
            $existing->delete();
            return redirect()->back()->with('success', 'Movie removed from watchlist!');
        } else {
            Watchlist::create([
                'user_id' => auth()->id(),
                'movie_id' => $request->movie_id,
                'type' => 'watchlist',
            ]);

            return redirect()->back()->with('success', 'Movie added to watchlist!');
        }
    }

    /**
     * Toggle movie in favorites for web interface
     */
    public function toggleFavoriteWeb(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
        ]);

        $existing = Watchlist::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->where('type', 'favorite')
            ->first();

        if ($existing) {
            $existing->delete();
            return redirect()->back()->with('success', 'Movie removed from favorites!');
        } else {
            Watchlist::create([
                'user_id' => auth()->id(),
                'movie_id' => $request->movie_id,
                'type' => 'favorite',
            ]);

            return redirect()->back()->with('success', 'Movie added to favorites!');
        }
    }

    /**
     * Show user's watchlist page
     */
    public function showWatchlist()
    {
        $watchlist = Watchlist::with(['movie.creator', 'movie.ratings'])
            ->where('user_id', auth()->id())
            ->where('type', 'watchlist')
            ->get();

        return view('movies.watchlist', compact('watchlist'));
    }

    /**
     * Show user's favorites page
     */
    public function showFavorites()
    {
        $favorites = Watchlist::with(['movie.creator', 'movie.ratings'])
            ->where('user_id', auth()->id())
            ->where('type', 'favorite')
            ->get();

        return view('movies.favorites', compact('favorites'));
    }
}
