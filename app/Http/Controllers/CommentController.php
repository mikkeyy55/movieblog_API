<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $comments = Comment::with(['user', 'movie'])->get();

        return response()->json([
            'comments' => $comments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'content' => 'required|string|max:1000',
        ]);

        // Check if movie exists
        $movie = Movie::findOrFail($request->movie_id);

        $comment = Comment::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $comment = Comment::with(['user', 'movie'])->findOrFail($id);

        return response()->json([
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        // Only allow user to update their own comment or admin
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to update this comment'
            ], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comment updated successfully',
            'comment' => $comment->load('user')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);

        // Only allow user to delete their own comment or admin
        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted successfully'
        ]);
    }

    /**
     * Store a comment for web interface
     */
    public function storeWeb(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'content' => 'required|string|max:1000',
        ]);

        Comment::create([
            'user_id' => auth()->id(),
            'movie_id' => $request->movie_id,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Update a comment for web interface
     */
    public function updateWeb(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized to update this comment.');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update(['content' => $request->content]);

        return redirect()->back()->with('success', 'Comment updated successfully!');
    }

    /**
     * Delete a comment for web interface
     */
    public function destroyWeb(string $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized to delete this comment.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }

    /**
     * Admin can delete any comment (API)
     */
    public function adminDestroy(string $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json([
            'message' => 'Comment deleted by admin successfully'
        ]);
    }
}
