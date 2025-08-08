<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource with search and filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Movie::with(['creator', 'comments', 'ratings', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount(['ratings', 'watchlists']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by genre
        if ($request->has('genre') && $request->genre) {
            $query->byGenre($request->genre);
        }

        // Filter by release year
        if ($request->has('year') && $request->year) {
            $query->byYear($request->year);
        }

        // Filter by minimum rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->byMinRating($request->min_rating);
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $query->byTag($request->tag);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'rating':
                $query->orderBy('ratings_avg_rating', $sortOrder);
                break;
            case 'year':
                $query->orderBy('release_year', $sortOrder);
                break;
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'popularity':
                $query->orderBy('watchlists_count', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $movies = $query->paginate($perPage);

        return response()->json($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'cover_image' => 'nullable|url|max:500',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        try {
            $movie = Movie::create([
                'title' => $request->title,
                'genre' => $request->genre,
                'description' => $request->description,
                'release_year' => $request->release_year,
                'cover_image' => $request->cover_image,
                'created_by' => auth()->id(),
            ]);

            // Add tags if provided
            if ($request->has('tags') && is_array($request->tags)) {
                foreach ($request->tags as $tag) {
                    if (!empty(trim($tag))) {
                        $movie->tags()->create(['tag' => trim($tag)]);
                    }
                }
            }

            return response()->json([
                'message' => 'Movie created successfully',
                'movie' => $movie->load(['creator', 'tags'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create movie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $movie = Movie::with(['creator', 'comments.user', 'ratings.user', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount(['ratings', 'watchlists'])
            ->findOrFail($id);

        // Add user-specific data if authenticated
        $userWatchlistStatus = null;
        if (auth()->check()) {
            $userWatchlistStatus = [
                'in_watchlist' => auth()->user()->watchlistMovies()->where('movie_id', $id)->exists(),
                'is_favorite' => auth()->user()->favoriteMovies()->where('movie_id', $id)->exists(),
            ];
        }

        return response()->json([
            'movie' => $movie,
            'user_status' => $userWatchlistStatus
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'genre' => 'sometimes|required|string|max:100',
            'description' => 'sometimes|required|string|min:10',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'cover_image' => 'nullable|url|max:500',
        ]);

        try {
            $movie->update($request->only([
                'title', 'genre', 'description', 'release_year', 'cover_image'
            ]));

            return response()->json([
                'message' => 'Movie updated successfully',
                'movie' => $movie->load('creator')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update movie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json([
                'message' => 'Access denied. Only admins can delete movies.'
            ], 403);
        }

        try {
            $movie = Movie::findOrFail($id);
            $movieTitle = $movie->title;
            $movie->delete();

            return response()->json([
                'message' => "Movie '$movieTitle' deleted successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete movie',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get trending movies (most commented/rated recently)
     */
    public function trending(Request $request): JsonResponse
    {
        $days = $request->get('days', 7); // Default to last 7 days
        $limit = $request->get('limit', 10);

        $movies = Movie::with(['creator', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount([
                'ratings',
                'comments' => function ($query) use ($days) {
                    $query->where('created_at', '>=', now()->subDays($days));
                },
                'ratings as recent_ratings_count' => function ($query) use ($days) {
                    $query->where('created_at', '>=', now()->subDays($days));
                }
            ])
            ->orderByDesc('comments_count')
            ->orderByDesc('recent_ratings_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'trending_movies' => $movies,
            'period_days' => $days
        ]);
    }

    /**
     * Get top-rated movies
     */
    public function topRated(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $minRatings = $request->get('min_ratings', 3); // Minimum number of ratings

        $movies = Movie::with(['creator', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->having('ratings_count', '>=', $minRatings)
            ->orderByDesc('ratings_avg_rating')
            ->orderByDesc('ratings_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'top_rated_movies' => $movies,
            'min_ratings_required' => $minRatings
        ]);
    }

    /**
     * Get most popular movies (most in watchlists/favorites)
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);

        $movies = Movie::with(['creator', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount(['ratings', 'watchlists'])
            ->orderByDesc('watchlists_count')
            ->orderByDesc('ratings_avg_rating')
            ->limit($limit)
            ->get();

        return response()->json([
            'popular_movies' => $movies
        ]);
    }

    /**
     * Get all available genres
     */
    public function genres(): JsonResponse
    {
        $genres = Movie::select('genre')
            ->distinct()
            ->whereNotNull('genre')
            ->orderBy('genre')
            ->pluck('genre');

        return response()->json([
            'genres' => $genres
        ]);
    }

    /**
     * Get all available tags
     */
    public function tags(): JsonResponse
    {
        $tags = \App\Models\MovieTag::select('tag')
            ->distinct()
            ->orderBy('tag')
            ->pluck('tag');

        return response()->json([
            'tags' => $tags
        ]);
    }

    /**
     * Get movies by specific genre
     */
    public function byGenre(Request $request, string $genre): JsonResponse
    {
        $movies = Movie::with(['creator', 'tags'])
            ->byGenre($genre)
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->orderByDesc('ratings_avg_rating')
            ->paginate($request->get('per_page', 12));

        return response()->json($movies);
    }
}
