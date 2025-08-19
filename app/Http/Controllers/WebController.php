<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class WebController extends Controller
{
        /**
     * Show the home page with movies list with filtering and search
     */
    public function index(Request $request)
    {
        $query = Movie::with(['creator', 'comments', 'ratings', 'tags'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by genre
        if ($request->has('genre') && $request->genre) {
            $query->where('genre', 'like', '%' . $request->genre . '%');
        }

        // Filter by release year
        if ($request->has('year') && $request->year) {
            $query->where('release_year', $request->year);
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
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $movies = $query->get();

        return view('movies.index', compact('movies'));
    }

    /**
     * Show a specific movie
     */
    public function show($id)
    {
        try {
            $movie = Movie::with(['creator', 'comments.user', 'ratings.user'])
                ->withAvg('ratings', 'rating')
                ->withCount('ratings')
                ->findOrFail($id);

            return view('movies.show', compact('movie'));
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Movie not found');
        }
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

        /**
     * Handle login - Step 1: Validate credentials and redirect to OTP
     */
    public function login(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Store user info in session for OTP verification
            $request->session()->put('pending_login_user_id', $user->id);
            $request->session()->put('pending_login_email', $user->email);

            // Logout the user temporarily until OTP is verified
            Auth::logout();

            // Send OTP to user's email
            try {
                $otp = $otpService->sendOtp($user->email, 'user');
                \Log::info("OTP sent to {$user->email}: {$otp}"); // For development - remove in production
            } catch (\Exception $e) {
                \Log::error('Failed to send OTP: ' . $e->getMessage());
                return back()->withErrors([
                    'email' => 'Failed to send verification code. Please try again.',
                ]);
            }

            // Redirect to OTP verification page
            return redirect()->route('otp.login', ['email' => $user->email])
                           ->with('info', 'Please enter the OTP sent to your email to complete login.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle OTP verification - Step 2: Complete login after OTP verification
     */
    public function verifyOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Check if we have pending login session
        $pendingUserId = $request->session()->get('pending_login_user_id');
        $pendingEmail = $request->session()->get('pending_login_email');

        if (!$pendingUserId || $pendingEmail !== $request->email) {
            return redirect()->route('login')->with('error', 'Invalid login session. Please try again.');
        }

        // Verify OTP using the service
        if ($otpService->verifyOtp($request->email, $request->otp)) {
            // Find the user and log them in
            $user = User::find($pendingUserId);
            if ($user && $user->email === $request->email) {
                Auth::login($user);
                $request->session()->forget(['pending_login_user_id', 'pending_login_email']);
                $request->session()->regenerate();
                return redirect()->intended('/')->with('success', 'Welcome back!');
            }
        }

        // Get remaining attempts for better error message
        $remainingAttempts = $otpService->getRemainingAttempts($request->email);

        if ($remainingAttempts <= 0) {
            // Clear session and redirect to login
            $request->session()->forget(['pending_login_user_id', 'pending_login_email']);
            return redirect()->route('login')->with('error', 'Too many failed attempts. Please start the login process again.');
        }

        return back()->withErrors([
            'otp' => "Invalid OTP. You have {$remainingAttempts} attempts remaining.",
        ]);
    }

    /**
     * Resend OTP for regular login
     */
    public function resendOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if we have pending login session
        $pendingUserId = $request->session()->get('pending_login_user_id');
        $pendingEmail = $request->session()->get('pending_login_email');

        if (!$pendingUserId || $pendingEmail !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login session. Please start over.',
            ], 400);
        }

        try {
            $otp = $otpService->sendOtp($request->email, 'user');
            \Log::info("OTP resent to {$request->email}: {$otp}"); // For development

            return response()->json([
                'success' => true,
                'message' => 'OTP has been resent to your email.',
                'otp' => $otp // For development - remove in production
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
            ], 500);
        }
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect('/')->with('success', 'Account created successfully!');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out.');
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        return view('auth.profile');
    }

    /**
     * Show movie creation form (admin only)
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied.');
        }

        return view('movies.create');
    }

    /**
     * Store a new movie (admin only)
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov,wmv,flv|max:102400',
            'tags' => 'nullable|string|max:500',
        ]);

        try {
            $coverImagePath = null;
            $videoPath = null;

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $coverImageName = time() . '_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('public/movies/covers', $coverImageName);
                $coverImagePath = str_replace('public/', 'storage/', $coverImagePath);
            }

            // Handle video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoName = time() . '_' . $video->getClientOriginalName();
                $videoPath = $video->storeAs('public/movies/videos', $videoName);
                $videoPath = str_replace('public/', 'storage/', $videoPath);
            }

            $movie = Movie::create([
                'title' => $request->title,
                'genre' => $request->genre,
                'description' => $request->description,
                'release_year' => $request->release_year,
                'cover_image' => $coverImagePath,
                'video_path' => $videoPath,
                'created_by' => Auth::id(),
            ]);

            // Add tags if provided
            if ($request->tags) {
                $tags = array_map('trim', explode(',', $request->tags));
                foreach ($tags as $tag) {
                    if (!empty($tag) && strlen($tag) <= 50) {
                        $movie->tags()->create(['tag' => $tag]);
                    }
                }
            }

            return redirect()->route('movies.show', $movie->id)
                ->with('success', 'Movie "' . $movie->title . '" created successfully!');

        } catch (\Exception $e) {
            \Log::error('Movie creation failed: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Failed to create movie. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show movie edit form (admin only)
     */
    public function edit($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied.');
        }

        $movie = Movie::findOrFail($id);
        return view('movies.edit', compact('movie'));
    }

    /**
     * Update a movie (admin only)
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Admin privileges required.');
        }

        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'genre' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'video' => 'nullable|mimes:mp4,avi,mov,wmv,flv|max:102400',
        ]);

        try {
            $updateData = $request->only([
                'title', 'genre', 'description', 'release_year'
            ]);

            // Handle cover image upload
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $coverImageName = time() . '_' . $coverImage->getClientOriginalName();
                $coverImagePath = $coverImage->storeAs('public/movies/covers', $coverImageName);
                $updateData['cover_image'] = str_replace('public/', 'storage/', $coverImagePath);
            }

            // Handle video upload
            if ($request->hasFile('video')) {
                $video = $request->file('video');
                $videoName = time() . '_' . $video->getClientOriginalName();
                $videoPath = $video->storeAs('public/movies/videos', $videoName);
                $updateData['video_path'] = str_replace('public/', 'storage/', $videoPath);
            }

            $movie->update($updateData);

            return redirect()->route('movies.show', $movie->id)
                ->with('success', 'Movie "' . $movie->title . '" updated successfully!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Failed to update movie. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a movie (admin only)
     */
    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return redirect('/')->with('error', 'Access denied. Only admins can delete movies.');
        }

        try {
            $movie = Movie::findOrFail($id);
            $movieTitle = $movie->title;
            $movie->delete();

            return redirect()->route('movies.index')->with('success', "Movie '$movieTitle' deleted successfully!");
        } catch (\Exception $e) {
            return redirect()->route('movies.index')->with('error', 'Failed to delete movie. Please try again.');
        }
    }

    /**
     * Show admin login form
     */
    public function showAdminLogin()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('movies.index')->with('success', 'Already logged in as admin!');
        }

        return view('auth.admin-login');
    }

        /**
     * Handle admin login - Step 1: Validate credentials and redirect to OTP
     */
    public function adminLogin(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Check if the authenticated user is an admin
            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Access denied. Admin credentials required.',
                ])->withInput($request->only('email'));
            }

            // Store admin info in session for OTP verification
            $request->session()->put('pending_admin_user_id', $user->id);
            $request->session()->put('pending_admin_email', $user->email);

            // Logout the admin temporarily until OTP is verified
            Auth::logout();

            // Send admin OTP to user's email
            try {
                $otp = $otpService->sendOtp($user->email, 'admin');
                \Log::info("Admin OTP sent to {$user->email}: {$otp}"); // For development - remove in production
            } catch (\Exception $e) {
                \Log::error('Failed to send admin OTP: ' . $e->getMessage());
                return back()->withErrors([
                    'email' => 'Failed to send admin verification code. Please try again.',
                ])->withInput($request->only('email'));
            }

            // Redirect to admin OTP verification page
            return redirect()->route('admin.otp.login', ['email' => $user->email])
                           ->with('info', 'Please enter the admin OTP sent to your email to complete login.');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle admin OTP verification - Step 2: Complete admin login after OTP verification
     */
    public function verifyAdminOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        // Check if we have pending admin login session
        $pendingUserId = $request->session()->get('pending_admin_user_id');
        $pendingEmail = $request->session()->get('pending_admin_email');

        if (!$pendingUserId || $pendingEmail !== $request->email) {
            return redirect()->route('admin.login')->with('error', 'Invalid admin login session. Please try again.');
        }

        // Verify admin OTP using the service
        if ($otpService->verifyOtp($request->email, $request->otp)) {
            // Find the user and verify they are an admin
            $user = User::find($pendingUserId);
            if ($user && $user->email === $request->email && $user->isAdmin()) {
                Auth::login($user);
                $request->session()->forget(['pending_admin_user_id', 'pending_admin_email']);
                $request->session()->regenerate();
                return redirect()->route('movies.index')->with('success', 'Welcome back, Admin!');
            }
        }

        // Get remaining attempts for better error message
        $remainingAttempts = $otpService->getRemainingAttempts($request->email);

        if ($remainingAttempts <= 0) {
            // Clear session and redirect to admin login
            $request->session()->forget(['pending_admin_user_id', 'pending_admin_email']);
            return redirect()->route('admin.login')->with('error', 'Too many failed attempts. Please start the admin login process again.');
        }

        return back()->withErrors([
            'otp' => "Invalid admin OTP. You have {$remainingAttempts} attempts remaining.",
        ]);
    }

    /**
     * Resend OTP for admin login
     */
    public function resendAdminOtp(Request $request, OtpService $otpService)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if we have pending admin login session
        $pendingUserId = $request->session()->get('pending_admin_user_id');
        $pendingEmail = $request->session()->get('pending_admin_email');

        if (!$pendingUserId || $pendingEmail !== $request->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid admin login session. Please start over.',
            ], 400);
        }

        try {
            $otp = $otpService->sendOtp($request->email, 'admin');
            \Log::info("Admin OTP resent to {$request->email}: {$otp}"); // For development

            return response()->json([
                'success' => true,
                'message' => 'Admin OTP has been resent to your email.',
                'otp' => $otp // For development - remove in production
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend admin OTP: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend admin OTP. Please try again.',
            ], 500);
        }
    }
}
