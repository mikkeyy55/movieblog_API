<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Movie API with Role-based Authentication using Laravel Sanctum
|
| Public routes: movie viewing, user registration/login
| User routes: commenting, rating (requires authentication)
| Admin routes: movie CRUD, comment management (requires admin token)
|
*/

// ==================== PUBLIC ROUTES ====================
// Anyone can access these routes

// Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/register', [AuthController::class, 'registerAdmin']);
Route::post('/admin/login', [AuthController::class, 'loginAdmin']);

// OTP Authentication (alternative)
Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/send-admin-otp', [OtpController::class, 'sendAdminOtp']);
Route::post('/verify-admin-otp', [OtpController::class, 'verifyAdminOtp']);

// Movies (public viewing)
Route::get('/movies', [MovieController::class, 'index']);
Route::get('/movies/{id}', [MovieController::class, 'show']);

// Movie discovery and filtering (public)
Route::get('/movies/trending/list', [MovieController::class, 'trending']);
Route::get('/movies/top-rated/list', [MovieController::class, 'topRated']);
Route::get('/movies/popular/list', [MovieController::class, 'popular']);
Route::get('/movies/genres/list', [MovieController::class, 'genres']);
Route::get('/movies/tags/list', [MovieController::class, 'tags']);
Route::get('/movies/genre/{genre}', [MovieController::class, 'byGenre']);

// Comments and Ratings (public viewing)
Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{id}', [CommentController::class, 'show']);
Route::get('/ratings', [RatingController::class, 'index']);
Route::get('/ratings/{id}', [RatingController::class, 'show']);

// ==================== PROTECTED ROUTES ====================
// Require valid authentication token

Route::middleware('auth:sanctum')->group(function () {

    // ==================== COMMON AUTH ROUTES ====================
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // ==================== USER ROUTES ====================
    // For authenticated users (both regular users and admins)

    // Comments - Users can create, edit their own comments
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    // Ratings - Users can rate movies
    Route::post('/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{id}', [RatingController::class, 'update']);
    Route::delete('/ratings/{id}', [RatingController::class, 'destroy']);

    // ==================== WATCHLIST & FAVORITES ====================
    // User watchlist and favorites management

    Route::get('/watchlist', [\App\Http\Controllers\WatchlistController::class, 'getWatchlist']);
    Route::get('/favorites', [\App\Http\Controllers\WatchlistController::class, 'getFavorites']);

    Route::post('/watchlist/add', [\App\Http\Controllers\WatchlistController::class, 'addToWatchlist']);
    Route::post('/favorites/add', [\App\Http\Controllers\WatchlistController::class, 'addToFavorites']);

    Route::delete('/watchlist/{movieId}', [\App\Http\Controllers\WatchlistController::class, 'removeFromWatchlist']);
    Route::delete('/favorites/{movieId}', [\App\Http\Controllers\WatchlistController::class, 'removeFromFavorites']);

    Route::post('/watchlist/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggleWatchlist']);
    Route::post('/favorites/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggleFavorite']);

    // ==================== ADMIN ONLY ROUTES ====================
    // Require admin token - only admins can access these

    Route::middleware('admin')->group(function () {
        // Movie Management - Admin only
        Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
        Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

        // Admin can delete any comment
        Route::delete('/admin/comments/{id}', [CommentController::class, 'adminDestroy']);

        // Admin can view all users
        Route::get('/admin/users', [AuthController::class, 'getAllUsers']);
    });
});
