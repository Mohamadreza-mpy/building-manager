<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Building;
use App\Services\AnnouncementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AnnouncementController extends Controller
{
    public function __construct(private AnnouncementService $service) {}

    public function index(Request $request, Building $building): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Announcement::class, $building]);

        return AnnouncementResource::collection($this->service->list($building))->additional(['success' => true, 'message' => 'فهرست اطلاعیه‌ها دریافت شد.']);
    }

    public function store(StoreAnnouncementRequest $request, Building $building): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'اطلاعیه با موفقیت ایجاد شد.', 'data' => new AnnouncementResource($this->service->create($building, $request->validated(), $request->user()))], 201);
    }
}
