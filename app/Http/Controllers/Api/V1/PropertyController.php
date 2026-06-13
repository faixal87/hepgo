<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyCollection;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = Property::query()
            ->publiclyVisible()
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        $this->applyFilters($query, $request);

        return $this->successResponse(
            (new PropertyCollection($query->paginate(12)->withQueryString()))->resolve($request)
        );
    }

    public function show(Property $property, Request $request): JsonResponse
    {
        $isPublic = Property::query()
            ->whereKey($property->getKey())
            ->publiclyVisible()
            ->exists();

        if (! $isPublic) {
            return $this->errorResponse([], 'Rumah sewa tidak ditemui.', 404);
        }

        $property->load(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        return $this->successResponse(
            (new PropertyResource($property))->resolve($request)
        );
    }

    private function applyFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $keyword = '%'.trim((string) $request->query('search')).'%';

            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('title', 'like', $keyword)
                    ->orWhere('address', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword)
                    ->orWhereHas('area', fn (Builder $areaQuery) => $areaQuery->where('name', 'like', $keyword));
            });
        }

        foreach (['area_id', 'category_id'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->query($field));
            }
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->query('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->query('max_price'));
        }

        if (in_array($request->query('status'), [
            PropertyAvailabilityStatus::AVAILABLE->value,
            PropertyAvailabilityStatus::FULL->value,
        ], true)) {
            $query->where('status', $request->query('status'));
        }

        if (in_array($request->query('gender_preference'), GenderPreference::values(), true)) {
            $query->where('gender_preference', $request->query('gender_preference'));
        }

        $facilityIds = collect($request->query('facilities', []))
            ->filter()
            ->map(fn ($facilityId) => (int) $facilityId)
            ->filter()
            ->values()
            ->all();

        if ($facilityIds !== []) {
            $query->whereHas('facilities', fn (Builder $facilityQuery) => $facilityQuery->whereIn('facilities.id', $facilityIds));
        }

        match ($request->query('sort')) {
            'harga_rendah', 'price_asc' => $query->orderBy('price')->latest('id'),
            'harga_tinggi', 'price_desc' => $query->orderByDesc('price')->latest('id'),
            'jarak_terdekat', 'distance' => $query->orderByRaw('distance_km IS NULL')->orderBy('distance_km')->latest('id'),
            default => $query->latest(),
        };
    }
}
