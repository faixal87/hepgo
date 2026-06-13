<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\GenderPreference;
use App\Enums\PropertyAvailabilityStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\PropertyIndexRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\PropertySummaryResource;
use App\Models\Property;
use App\Support\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    use ApiResponse;

    public function index(PropertyIndexRequest $request): JsonResponse
    {
        $query = Property::query()
            ->publiclyVisible()
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        $this->applyFilters($query, $request);
        $properties = $query
            ->paginate((int) $request->integer('per_page', 12))
            ->withQueryString();

        return $this->successResponse(
            PropertySummaryResource::collection($properties->getCollection())->resolve($request),
            'Senarai data berjaya dipaparkan.',
            200,
            [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
                'links' => [
                    'first' => $properties->url(1),
                    'last' => $properties->url($properties->lastPage()),
                    'prev' => $properties->previousPageUrl(),
                    'next' => $properties->nextPageUrl(),
                ],
            ]
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
        $validated = $request->validated();

        if (filled($validated['search'] ?? null)) {
            $keyword = '%'.trim((string) $validated['search']).'%';

            $query->where(function (Builder $query) use ($keyword): void {
                $query
                    ->where('title', 'like', $keyword)
                    ->orWhere('address', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword)
                    ->orWhereHas('area', fn (Builder $areaQuery) => $areaQuery->where('name', 'like', $keyword));
            });
        }

        foreach (['area_id', 'category_id'] as $field) {
            if (filled($validated[$field] ?? null)) {
                $query->where($field, $validated[$field]);
            }
        }

        if (filled($validated['min_price'] ?? null)) {
            $query->where('price', '>=', (float) $validated['min_price']);
        }

        if (filled($validated['max_price'] ?? null)) {
            $query->where('price', '<=', (float) $validated['max_price']);
        }

        if (in_array($validated['status'] ?? null, [
            PropertyAvailabilityStatus::AVAILABLE->value,
            PropertyAvailabilityStatus::FULL->value,
        ], true)) {
            $query->where('status', $validated['status']);
        }

        if (in_array($validated['gender_preference'] ?? null, GenderPreference::values(), true)) {
            $query->where('gender_preference', $validated['gender_preference']);
        }

        $facilityIds = collect($validated['facilities'] ?? [])
            ->filter()
            ->map(fn ($facilityId) => (int) $facilityId)
            ->filter()
            ->values()
            ->all();

        if ($facilityIds !== []) {
            $query->whereHas('facilities', fn (Builder $facilityQuery) => $facilityQuery->whereIn('facilities.id', $facilityIds));
        }

        match ($validated['sort'] ?? 'latest') {
            'price_low', 'price_asc' => $query->orderBy('price')->latest('id'),
            'price_high', 'price_desc' => $query->orderByDesc('price')->latest('id'),
            'distance_near', 'distance' => $query->orderByRaw('distance_km IS NULL')->orderBy('distance_km')->latest('id'),
            default => $query->latest(),
        };
    }
}
