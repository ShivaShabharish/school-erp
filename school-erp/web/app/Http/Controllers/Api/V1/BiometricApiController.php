<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class BiometricApiController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    /**
     * Biometric machine punch endpoint.
     * POST /api/v1/biometric/punch
     */
    public function punch(Request $request): JsonResponse
    {
        // Validate shared secret key header
        $secretKey = $request->header('X-Biometric-Key')
            ?? $request->header('X-Device-Secret')
            ?? $request->input('secret_key');

        $configuredKey = config('services.biometric.secret', 'SCHOOL_BIO_SEC_2026');

        if (!$secretKey || $secretKey !== $configuredKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized biometric device. Invalid security key header.',
            ], 401);
        }

        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:50'],
            'staff_id' => ['required'], // can be numeric ID or employee_code string
            'timestamp' => ['nullable', 'date'],
        ]);

        try {
            $result = $this->attendanceService->recordBiometricPunch(
                deviceId: $validated['device_id'],
                staffId: $validated['staff_id'],
                timestamp: $validated['timestamp'] ?? null
            );

            return response()->json($result, 200);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal error processing biometric punch: ' . $e->getMessage(),
            ], 500);
        }
    }
}
