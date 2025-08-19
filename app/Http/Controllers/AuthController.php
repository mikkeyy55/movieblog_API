<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Otp;
use App\Mail\SendOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;



class AuthController extends Controller
{
    /**
     * Register a new user
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
        'is_verified' => false,
    ]);

    // Use OtpService instead of direct Otp model
    $otpService = app(OtpService::class);
    $otp = $otpService->sendOtp($request->email);

    return redirect()->route('verify.otp.form')
                   ->with('email', $user->email)
                   ->with('success', 'A verification code has been sent to your email.');
}

    /**
     * Login user
     */
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('email', 'password'))) {
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    $user = User::where('email', $request->email)->firstOrFail();

    // For API requests
    if ($request->wantsJson()) {
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    // For web requests
    $request->session()->regenerate();
    return redirect()->intended('/')->with('success', 'Logged in successfully');
}
    /**
     * Logout user
     */
    public function logout(Request $request)
{
    Auth::logout();
    
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/')->with('status', 'You have been logged out.');
}

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ]);
    }

    /**
     * Register a new admin user
     */
    public function registerAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'admin_key' => 'required|string', // You can add a secret admin key for registration
        ]);

        // You can add validation for admin_key here
        if ($request->admin_key !== config('app.admin_registration_key', 'admin_secret_key')) {
            return response()->json([
                'message' => 'Invalid admin registration key'
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        $token = $user->createToken('admin_auth_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Admin registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login admin user
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        // Create token with admin abilities
        $token = $user->createToken('admin_auth_token', ['admin'])->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Get all users (Admin only)
     */
    public function getAllUsers(Request $request)
    {
        $users = User::with(['comments', 'ratings'])->get();

        return response()->json([
            'users' => $users
        ]);
    }

    public function showVerifyOtpForm()
{
    return view('auth.otp-login')->with('email', session('email'));
}

// public function verifyOtp(Request $request)
// {
//     $request->validate([
//         'email' => 'required|email',
//         'otp' => 'required|numeric',
//     ]);

//     $user = User::where('email', $request->email)->firstOrFail();
    
//     $otpService = app(OtpService::class);
//     if (!$otpService->verifyOtp($request->email, $request->otp)) {
//         return back()->withErrors(['otp' => 'Invalid or expired OTP']);
//     }

//     $user->is_verified = true;
//     $user->save();

//     Auth::login($user);

//     return redirect('/')->with('success', 'Your account has been verified and you are now logged in.');
// }

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|numeric',
    ]);

    // Log the incoming request
    \Log::info("OTP Verification Attempt", [
        'email' => $request->email,
        'otp' => $request->otp,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
    ]);

    $user = User::where('email', $request->email)->firstOrFail();
    $otpService = app(OtpService::class);

    // Debugging: Check if OTP exists before verification
    \Log::debug("Pre-verification check", [
        'has_otp' => $otpService->hasOtp($request->email),
        'remaining_attempts' => $otpService->getRemainingAttempts($request->email)
    ]);

    if (!$otpService->verifyOtp($request->email, $request->otp)) {
        \Log::warning("OTP Verification Failed", [
            'email' => $request->email,
            'remaining_attempts' => $otpService->getRemainingAttempts($request->email)
        ]);
        return back()->withErrors(['otp' => 'Invalid or expired OTP']);
    }

    \Log::info("OTP Verification Successful", [
        'user_id' => $user->id,
        'email' => $user->email
    ]);

    $user->is_verified = true;
    $user->save();

    Auth::login($user);

    return redirect('/')->with('success', 'Your account has been verified and you are now logged in.');
}

/**
 * Logout user from web session
 */
/**
 * Logout user from web session
 */
public function webLogout(Request $request)
{
    // Check if user is authenticated via API (has tokens)
    if ($request->user() && $request->user()->currentAccessToken()) {
        $request->user()->currentAccessToken()->delete();
    }
    
    // Logout from session
    Auth::logout();
    
    // Invalidate session
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/')->with('success', 'Logged out successfully');
}

}


