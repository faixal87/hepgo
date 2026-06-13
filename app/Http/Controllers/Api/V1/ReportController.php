<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePropertyReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\PropertyReport;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    use ApiResponse;

    public function store(StorePropertyReportRequest $request): JsonResponse
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

        return $this->successResponse(
            (new ReportResource($report->load('property')))->resolve($request),
            'Aduan anda berjaya dihantar. Pihak HEP akan menyemak maklumat ini.',
            201
        );
    }
}
