<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
class AuthController extends Controller
{
    public function studentLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $student = Student::where('email', $credentials['email'])->first();

        if (!$student || !Hash::check($credentials['password'], $student->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $student->update(['last_login_at' => now()]);

        $token = JWTAuth::fromUser($student);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $student,
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function teacherLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $teacher = Teacher::where('email', $credentials['email'])->first();

        if (!$teacher || !Hash::check($credentials['password'], $teacher->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = JWTAuth::fromUser($teacher);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => $teacher,
            'expires_in' => config('jwt.ttl') * 60
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Token invalid or missing'], 401);
        }
    }

    public function getUser()
    {
        try {
            $user = auth('student')->user() ?? auth('teacher')->user();

            if (!$user) {
                return response()->json(['error' => 'User not found or not authenticated'], 401);
            }

            return response()->json($user);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch user profile, Token invalid or missing'], 401);
        }
    }

    // public function refresh()
    // {
    //     return response()->json([
    //         'access_token' => JWTAuth::refresh(JWTAuth::getToken()),
    //         'token_type' => 'bearer',
    //         'expires_in' => config('jwt.ttl') * 60
    //     ]);
    // }
}