<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $ratings = Rating::with(['user', 'movie'])->get();

        return response()->json([
            'ratings' => $ratings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|between:1,5',
        ]);

        // Check if movie exists
        $movie = Movie::findOrFail($request->movie_id);

        // Check if user already rated this movie
        $existingRating = Rating::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->first();

        if ($existingRating) {
            return response()->json([
                'message' => 'You have already rated this movie. Use update to change your rating.'
            ], 400);
        }

        $rating = Rating::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'rating' => $request->rating,
        ]);

        return response()->json([
            'message' => 'Rating added successfully',
            'rating' => $rating->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $rating = Rating::with(['user', 'movie'])->findOrFail($id);

        return response()->json([
            'rating' => $rating
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $rating = Rating::findOrFail($id);

        // Only allow user to update their own rating
        if ($rating->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to update this rating'
            ], 403);
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ]);

        $rating->update([
            'rating' => $request->rating,
        ]);

        return response()->json([
            'message' => 'Rating updated successfully',
            'rating' => $rating->load('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $rating = Rating::findOrFail($id);

        // Only allow user to delete their own rating
        if ($rating->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized to delete this rating'
            ], 403);
        }

        $rating->delete();

        return response()->json([
            'message' => 'Rating deleted successfully'
        ]);
    }

    /**
     * Store or update a rating for web interface
     */
    public function storeWeb(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|between:1,5',
        ]);

        // Check if user already rated this movie
        $existingRating = Rating::where('user_id', auth()->id())
            ->where('movie_id', $request->movie_id)
            ->first();

        if ($existingRating) {
            $existingRating->update(['rating' => $request->rating]);
            return redirect()->back()->with('success', 'Rating updated successfully!');
        }

        Rating::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'rating' => $request->rating,
        ]);

        return redirect()->back()->with('success', 'Rating added successfully!');
    }

    /**
     * Update a rating for web interface
     */
    public function updateWeb(Request $request, string $id)
    {
        $rating = Rating::findOrFail($id);

        if ($rating->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized to update this rating.');
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
        ]);

        $rating->update(['rating' => $request->rating]);

        return redirect()->back()->with('success', 'Rating updated successfully!');
    }

    /**
     * Delete a rating for web interface
     */
    public function destroyWeb(string $id)
    {
        $rating = Rating::findOrFail($id);

        if ($rating->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized to delete this rating.');
        }

        $rating->delete();

        return redirect()->back()->with('success', 'Rating deleted successfully!');
    }
}
