<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials provided.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'User account is deactivated. Please contact school administration.',
            ], 403);
        }

        $deviceName = $validated['device_name'] ?? ($request->header('User-Agent') ?: 'ExpoMobileDevice');
        $token = $user->createToken($deviceName, ['mobile-access'])->plainTextToken;

        AuditLog::record('api_token_issued', $user, null, [
            'email' => $user->email,
            'device' => $deviceName,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Authentication successful.',
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['studentProfile.schoolClass', 'studentProfile.section', 'staffProfile']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'phone' => $user->phone,
                'student_profile' => $user->studentProfile,
                'staff_profile' => $user->staffProfile,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        AuditLog::record('api_token_revoked', $user);

        return response()->json([
            'status' => 'success',
            'message' => 'Token revoked successfully. Logged out from device.',
        ]);
    }
}
