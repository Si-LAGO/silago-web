<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'nim' => $request->nim,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'active',
        ]);

        $code = rand(100000, 999999);
        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        Log::info('Email verification code for ' . $user->email . ' is ' . $code);

        return response()->json([
            'message' => 'Registrasi berhasil. Silakan cek email untuk kode verifikasi.',
            'user' => new UserResource($user)
        ], 201);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $cachedCode = Cache::get('email_verify_' . $request->email);

        if (!$cachedCode || $cachedCode != $request->code) {
            return response()->json(['message' => 'Kode verifikasi tidak valid atau kedaluwarsa.', 'errors' => ['code' => ['Kode tidak valid']]], 400);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->update(['email_verified_at' => now()]);
        Cache::forget('email_verify_' . $request->email);

        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'user' => new UserResource($user)
        ]);
    }

    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $key = 'resend_verification_' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            return response()->json(['message' => 'Terlalu banyak percobaan. Tunggu beberapa saat.'], 429);
        }
        RateLimiter::hit($key, 60);

        $user = User::where('email', $request->email)->firstOrFail();
        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email sudah diverifikasi.'], 400);
        }

        $code = rand(100000, 999999);
        Cache::put('email_verify_' . $user->email, $code, now()->addMinutes(15));
        Log::info('Resend Email verification code for ' . $user->email . ' is ' . $code);

        return response()->json(['message' => 'Kode verifikasi telah dikirim ulang.']);
    }

    public function login(LoginRequest $request)
    {
        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json(['message' => 'Terlalu banyak percobaan login. Silakan coba lagi nanti.', 'errors' => []], 429);
        }

        $loginInput = $request->login;
        $attempt = false;

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $attempt = Auth::attempt(['email' => $loginInput, 'password' => $request->password]);
        } else {
            $attempt = Auth::attempt(['nim' => $loginInput, 'password' => $request->password]);
            if (!$attempt) {
                $attempt = Auth::attempt(['username' => $loginInput, 'password' => $request->password]);
            }
        }

        if (!$attempt) {
            RateLimiter::hit($key);
            return response()->json([
                'message' => 'Email/NIM atau kata sandi tidak cocok.',
                'errors' => ['login' => ['Email/NIM atau kata sandi salah.']]
            ], 401);
        }

        $user = Auth::user();
        if ($user->status === 'suspended') {
            Auth::logout();
            return response()->json(['message' => 'Akun ditangguhkan.', 'errors' => []], 401);
        }

        RateLimiter::clear($key);

        $token = $user->createToken('silago_api')->plainTextToken;

        if ($request->filled('fcm_token')) {
            $user->update(['fcm_token' => $request->fcm_token]);
        }

        return response()->json([
            'token' => $token,
            'user' => new UserResource($user)
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    public function forgotPassword(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function resetPassword(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
