<?php

namespace App\Http\Controllers;

use App\Enums\PropertyAvailabilityStatus;
use App\Enums\RecordStatus;
use App\Enums\VerificationStatus;
use App\Models\Area;
use App\Models\Category;
use App\Models\Property;
use Illuminate\View\View;

class PublicPortalController extends Controller
{
    public function home(): View
    {
        $verifiedCount = Property::query()
            ->publiclyVisible()
            ->count();

        $availableCount = Property::query()
            ->publiclyVisible()
            ->where('status', PropertyAvailabilityStatus::AVAILABLE->value)
            ->count();

        $areaCount = Area::query()
            ->whereHas('properties', fn ($query) => $query->publiclyVisible())
            ->count();

        $latestProperties = Property::query()
            ->publiclyVisible()
            ->where('status', PropertyAvailabilityStatus::AVAILABLE->value)
            ->with(['area', 'category', 'facilities', 'images', 'owner', 'thumbnailImage'])
            ->latest()
            ->limit(4)
            ->get();

        $areas = Area::query()
            ->where('status', RecordStatus::ACTIVE->value)
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->where('status', RecordStatus::ACTIVE->value)
            ->orderBy('name')
            ->get();

        return view('public.home', [
            'areaCount' => $areaCount,
            'areas' => $areas,
            'availableCount' => $availableCount,
            'categories' => $categories,
            'latestProperties' => $latestProperties,
            'verifiedCount' => $verifiedCount,
        ]);
    }

    public function show(Property $property): View
    {
        abort_unless(
            Property::query()
                ->whereKey($property->getKey())
                ->publiclyVisible()
                ->where('verification_status', VerificationStatus::VERIFIED->value)
                ->exists(),
            404
        );

        $property->load([
            'area',
            'category',
            'facilities',
            'images' => fn ($query) => $query
                ->orderByDesc('is_thumbnail')
                ->orderBy('sort_order'),
            'owner',
            'thumbnailImage',
        ]);

        return view('public.properties.show', [
            'property' => $property,
        ]);
    }
}
