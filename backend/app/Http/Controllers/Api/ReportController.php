<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReportResource;
use App\Models\Building;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $service) {}

    public function summary(Building $building): JsonResponse
    {
        $this->authorize('view', $building);

        return response()->json(['success' => true, 'message' => 'گزارش ساختمان دریافت شد.', 'data' => new ReportResource($this->service->summary($building))]);
    }
}
