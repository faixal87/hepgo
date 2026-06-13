<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function successResponse(array $data = [], string $message = 'Berjaya', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => (object) $data,
        ], $status);
    }

    /**
     * @param  array<string, mixed>  $errors
     */
    protected function errorResponse(array $errors = [], string $message = 'Ralat', int $status = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => (object) $errors,
        ], $status);
    }
}
