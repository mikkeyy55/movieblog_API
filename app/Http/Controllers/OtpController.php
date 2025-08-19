<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP for login
     */
    public function sendOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

        // Generate OTP
        $otp = $this->otpService->generateOtp($request->email);

        // For demo purposes, we'll return the OTP in response
        // In production, you would send this via email
        return response()->json([
            'message' => 'OTP sent successfully',
            'otp' => $otp->otp, // Remove this in production
            'expires_in' => 10 // minutes
        ]);
    }

public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|string|size:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json([
            'message' => 'User not found'
        ], 404);
    }

    // Verify OTP
    if (!$this->otpService->verifyOtp($request->email, $request->otp)) {
        return response()->json([
            'message' => 'Invalid or expired OTP'
        ], 400);
    }

    // Login user
    Auth::login($user);
    
    // For API
    if ($request->wantsJson()) {
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }
    
    // For web
    return redirect()->intended('/')->with('success', 'Logged in successfully');
}

    /**
     * Send OTP for admin login
     */
    public function sendAdminOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Admin access denied'
            ], 403);
        }

        try {
            // Send OTP via email
            $this->otpService->sendOtp($request->email, 'admin');

            return response()->json([
                'message' => 'Admin OTP sent successfully to your email',
                'expires_in' => 10 // minutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send admin OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Verify admin OTP and login
     */
    public function verifyAdminOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'message' => 'Admin access denied'
            ], 403);
        }

        // Verify OTP
        if (!$this->otpService->verifyOtp($request->email, $request->otp)) {
            return response()->json([
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // Login user
        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Admin login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
