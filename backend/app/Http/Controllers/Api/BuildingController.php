<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use App\Services\BuildingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BuildingController extends Controller
{

    public function __construct(
        private BuildingService $service
    ) {}


    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Building::class);

        return BuildingResource::collection($this->service->listFor($request->user()))
            ->additional(['success' => true, 'message' => 'فهرست ساختمان‌ها دریافت شد.']);
    }

    public function store(StoreBuildingRequest $request): JsonResponse
    {
        $building = $this->service->create($request->validated(), $request->user());

        return response()->json([
            'success' => true,
            'message' => 'ساختمان با موفقیت ایجاد شد.',
            'data' => new BuildingResource($building),
        ], 201);
    }

    public function show(Building $building): JsonResponse
    {
        $this->authorize('view', $building);

        return response()->json(['success' => true, 'message' => 'اطلاعات ساختمان دریافت شد.', 'data' => new BuildingResource($this->service->find($building))]);
    }

    public function update(UpdateBuildingRequest $request, Building $building): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'ساختمان با موفقیت ویرایش شد.', 'data' => new BuildingResource($this->service->update($building, $request->validated()))]);
    }

    public function destroy(Request $request, Building $building): JsonResponse
    {
        $this->authorize('delete', $building);
        $this->service->delete($building);

        return response()->json(['success' => true, 'message' => 'ساختمان با موفقیت حذف شد.', 'data' => null]);
    }
}
