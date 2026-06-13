<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RecordStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FacilityResource;
use App\Models\Area;
use App\Models\Category;
use App\Models\Facility;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    use ApiResponse;

    public function areas(Request $request): JsonResponse
    {
        return $this->successResponse([
            'items' => AreaResource::collection(
                Area::query()
                    ->where('status', RecordStatus::ACTIVE->value)
                    ->orderBy('name')
                    ->get()
            )->resolve($request),
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        return $this->successResponse([
            'items' => CategoryResource::collection(
                Category::query()
                    ->where('status', RecordStatus::ACTIVE->value)
                    ->orderBy('name')
                    ->get()
            )->resolve($request),
        ]);
    }

    public function facilities(Request $request): JsonResponse
    {
        return $this->successResponse([
            'items' => FacilityResource::collection(
                Facility::query()
                    ->orderBy('name')
                    ->get()
            )->resolve($request),
        ]);
    }
}
