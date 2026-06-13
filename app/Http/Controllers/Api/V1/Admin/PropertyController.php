<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StorePropertyRequest;
use App\Http\Requests\Api\V1\UpdatePropertyAvailabilityRequest;
use App\Http\Requests\Api\V1\UpdatePropertyRequest;
use App\Http\Requests\Api\V1\VerifyPropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyStatusService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $properties = Property::query()
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage'])
            ->latest()
            ->paginate(15);

        return $this->successResponse(
            PropertyResource::collection($properties->getCollection())->resolve($request),
            'Senarai data berjaya dipaparkan.',
            200,
            [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        );
    }

    public function store(StorePropertyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $facilityIds = $data['facilities'] ?? [];
        unset($data['facilities']);

        $property = Property::create([
            ...$data,
            'created_by' => $request->user()->id,
        ]);

        if ($facilityIds !== []) {
            $property->facilities()->sync($facilityIds);
        }

        $property->load(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        return $this->successResponse(
            (new PropertyResource($property))->resolve($request),
            'Rumah sewa berjaya ditambah.',
            201
        );
    }

    public function update(UpdatePropertyRequest $request, Property $property): JsonResponse
    {
        $data = $request->validated();
        $facilityIds = $data['facilities'] ?? null;
        unset($data['facilities']);

        $property->update($data);

        if (is_array($facilityIds)) {
            $property->facilities()->sync($facilityIds);
        }

        $property->load(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        return $this->successResponse(
            (new PropertyResource($property))->resolve($request),
            'Rumah sewa berjaya dikemaskini.'
        );
    }

    public function availability(UpdatePropertyAvailabilityRequest $request, Property $property): JsonResponse
    {
        $property = app(PropertyStatusService::class)->updateAvailability(
            $property,
            $request->validated('status'),
            $request->validated('remarks')
        );

        $property->load(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        return $this->successResponse(
            (new PropertyResource($property))->resolve($request),
            'Status rumah sewa berjaya dikemaskini.'
        );
    }

    public function verify(VerifyPropertyRequest $request, Property $property): JsonResponse
    {
        $property = app(PropertyStatusService::class)->updateVerification(
            $property,
            $request->validated('verification_status'),
            $request->validated('remarks')
        );

        $property->load(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage']);

        return $this->successResponse(
            (new PropertyResource($property))->resolve($request),
            'Status pengesahan rumah sewa berjaya dikemaskini.'
        );
    }
}
