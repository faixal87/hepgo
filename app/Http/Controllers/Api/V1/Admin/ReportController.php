<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ResolveReportRequest;
use App\Http\Resources\ReportResource;
use App\Models\PropertyReport;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $reports = PropertyReport::query()
            ->with(['property', 'handledBy'])
            ->latest()
            ->paginate(15);

        return $this->successResponse(
            ReportResource::collection($reports->getCollection())->resolve($request),
            'Senarai data berjaya dipaparkan.',
            200,
            [
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
                'per_page' => $reports->perPage(),
                'total' => $reports->total(),
            ]
        );
    }

    public function resolve(ResolveReportRequest $request, PropertyReport $report): JsonResponse
    {
        $report->markAs(
            ReportStatus::from($request->validated('status')),
            $request->validated('admin_remarks')
        );

        return $this->successResponse(
            (new ReportResource($report->load(['property', 'handledBy'])))->resolve($request),
            'Status aduan berjaya dikemaskini.'
        );
    }
}
