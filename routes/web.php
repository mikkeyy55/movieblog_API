<?php

use App\Http\Controllers\WebController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('/movies', [WebController::class, 'index'])->name('movies.index');

// Authentication routes
Route::get('/login', [WebController::class, 'showLogin'])->name('login');
Route::post('/login', [WebController::class, 'login']);
Route::post('/verify-otp', [WebController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/resend-otp', [WebController::class, 'resendOtp'])->name('resend.otp');
Route::get('/register', [WebController::class, 'showRegister'])->name('register');
Route::post('/register', [WebController::class, 'register']);
Route::post('/logout', [WebController::class, 'logout'])->name('logout');

// Admin routes
Route::get('/admin', [WebController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin', [WebController::class, 'adminLogin'])->name('admin.login.post');
Route::post('/verify-admin-otp', [WebController::class, 'verifyAdminOtp'])->name('verify.admin.otp');
Route::post('/resend-admin-otp', [WebController::class, 'resendAdminOtp'])->name('resend.admin.otp');

// OTP routes
Route::get('/otp-login', function (Request $request) {
    $email = $request->get('email');
    return view('auth.otp-login', compact('email'));
})->name('otp.login');

Route::get('/admin-otp', function (Request $request) {
    $email = $request->get('email');
    return view('auth.admin-otp-login', compact('email'));
})->name('admin.otp.login');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [WebController::class, 'profile'])->name('profile');

    // Movie management (Admin only)
    Route::get('/movies/create', [WebController::class, 'create'])->name('movies.create');
    Route::post('/movies', [WebController::class, 'store'])->name('movies.store');
    Route::get('/movies/{id}', [WebController::class, 'show'])->name('movies.show');
    Route::get('/movies/{id}/edit', [WebController::class, 'edit'])->name('movies.edit');
    Route::put('/movies/{id}', [WebController::class, 'update'])->name('movies.update');
    Route::delete('/movies/{id}', [WebController::class, 'destroy'])->name('movies.destroy');

    // Comments (AJAX endpoints)
    Route::post('/comments', [CommentController::class, 'storeWeb'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'updateWeb'])->name('comments.update');
    Route::delete('/comments/{id}', [CommentController::class, 'destroyWeb'])->name('comments.destroy');

    // Ratings (AJAX endpoints)
    Route::post('/ratings', [RatingController::class, 'storeWeb'])->name('ratings.store');
    Route::put('/ratings/{id}', [RatingController::class, 'updateWeb'])->name('ratings.update');
    Route::delete('/ratings/{id}', [RatingController::class, 'destroyWeb'])->name('ratings.destroy');

    // Watchlist & Favorites (AJAX endpoints)
    Route::post('/watchlist/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggleWatchlistWeb'])->name('watchlist.toggle');
    Route::post('/favorites/toggle', [\App\Http\Controllers\WatchlistController::class, 'toggleFavoriteWeb'])->name('favorites.toggle');
    Route::get('/watchlist', [\App\Http\Controllers\WatchlistController::class, 'showWatchlist'])->name('watchlist.show');
    Route::get('/favorites', [\App\Http\Controllers\WatchlistController::class, 'showFavorites'])->name('favorites.show');

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/movies', [AdminController::class, 'movies'])->name('admin.movies');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
        Route::delete('/admin/movies/{id}', [AdminController::class, 'deleteMovie'])->name('admin.movies.delete');
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
        Route::patch('/admin/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    });
});

// Temporary test route for admin login (remove in production)
Route::get('/test-admin-login', function () {
    $user = \App\Models\User::where('email', 'moviebloggroup3@gmail.com')->first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect()->route('movies.create')->with('success', 'Test admin login successful!');
    }
    return redirect('/')->with('error', 'Admin user not found');
})->name('test.admin.login');
