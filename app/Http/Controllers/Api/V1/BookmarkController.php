<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertySummaryResource;
use App\Models\Property;
use App\Models\PropertyBookmark;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $properties = Property::query()
            ->publiclyVisible()
            ->whereHas('bookmarks', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage'])
            ->latest('properties.updated_at')
            ->get();

        return $this->successResponse(
            PropertySummaryResource::collection($properties)->resolve($request),
            'Senarai rumah simpanan berjaya dipaparkan.'
        );
    }

    public function store(Request $request, Property $property): JsonResponse
    {
        if (! $this->isPublicProperty($property)) {
            return $this->errorResponse([], 'Rumah sewa tidak ditemui.', 404);
        }

        PropertyBookmark::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'property_id' => $property->id,
        ]);

        return $this->successResponse([
            'property_id' => $property->id,
            'is_bookmarked' => true,
        ], 'Rumah sewa berjaya disimpan.');
    }

    public function destroy(Request $request, Property $property): JsonResponse
    {
        PropertyBookmark::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->delete();

        return $this->successResponse([
            'property_id' => $property->id,
            'is_bookmarked' => false,
        ], 'Rumah sewa berjaya dibuang daripada simpanan.');
    }

    private function isPublicProperty(Property $property): bool
    {
        return Property::query()
            ->whereKey($property->getKey())
            ->publiclyVisible()
            ->exists();
    }
}
