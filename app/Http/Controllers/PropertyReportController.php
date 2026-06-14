<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Http\Requests\StorePropertyReportRequest;
use App\Models\Property;
use App\Models\PropertyReport;
use App\Services\SystemNotificationService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PropertyReportController extends Controller
{
    public function create(): View
    {
        return view('public.reports.create', [
            'properties' => $this->reportableProperties(),
            'property' => null,
            'reportTypes' => ReportType::options(),
        ]);
    }

    public function createForProperty(Property $property): View
    {
        abort_unless(
            Property::query()
                ->whereKey($property->getKey())
                ->publiclyVisible()
                ->exists(),
            404
        );

        $property->load(['area', 'category']);

        return view('public.reports.create', [
            'properties' => $this->reportableProperties(),
            'property' => $property,
            'reportTypes' => ReportType::options(),
        ]);
    }

    public function store(
        StorePropertyReportRequest $request,
        SystemNotificationService $notificationService,
    ): RedirectResponse
    {
        $report = PropertyReport::create([
            ...$request->safe()->only([
                'property_id',
                'reporter_name',
                'reporter_phone',
                'reporter_email',
                'report_type',
                'message',
            ]),
            'status' => ReportStatus::NEW,
        ]);

        $notificationService->notifyNewReport($report);

        return redirect()
            ->route('reports.create')
            ->with('status', 'Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.');
    }

    private function reportableProperties(): Collection
    {
        return Property::query()
            ->publiclyVisible()
            ->with(['area'])
            ->orderBy('title')
            ->get();
    }
}
