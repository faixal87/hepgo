<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AppConfigController extends Controller
{
    use ApiResponse;

    public function __invoke(): JsonResponse
    {
        return $this->successResponse([
            'app_name' => 'Portal Rumah Sewa HEP',
            'campus_name' => 'POLIMAS',
            'campus_location' => Property::POLIMAS_DESTINATION,
            'support_contact' => null,
            'version' => '1.0.0',
            'features' => [
                'public_listing' => true,
                'aduan' => true,
                'login' => true,
                'bookmark' => true,
                'owner_portal' => false,
            ],
        ], 'Konfigurasi aplikasi berjaya dipaparkan.');
    }
}
