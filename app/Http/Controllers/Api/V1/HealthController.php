<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    use ApiResponse;

    public function __invoke(): JsonResponse
    {
        return $this->successResponse([
            'status' => 'aktif',
            'service' => config('app.name'),
            'version' => 'v1',
        ]);
    }
}
