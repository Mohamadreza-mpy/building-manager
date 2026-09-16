<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApartmentRequest;
use App\Http\Requests\UpdateApartmentRequest;
use App\Http\Resources\ApartmentResource;
use App\Models\Apartment;
use App\Models\Building;
use App\Services\ApartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApartmentController extends Controller
{
    public function __construct(private ApartmentService $service) {}

    public function index(Request $request, Building $building): AnonymousResourceCollection
    {
        $this->authorize('view', $building);

        return ApartmentResource::collection($this->service->list($building))
            ->additional(['success' => true, 'message' => 'فهرست واحدها دریافت شد.']);
    }

    public function store(StoreApartmentRequest $request, Building $building): JsonResponse
    {
        $apartment = $this->service->create($building, $request->validated());

        return response()->json(['success' => true, 'message' => 'واحد با موفقیت ایجاد شد.', 'data' => new ApartmentResource($apartment)], 201);
    }

    public function show(Apartment $apartment): JsonResponse
    {
        $this->authorize('view', $apartment);

        return response()->json(['success' => true, 'message' => 'اطلاعات واحد دریافت شد.', 'data' => new ApartmentResource($this->service->find($apartment))]);
    }

    public function update(UpdateApartmentRequest $request, Apartment $apartment): JsonResponse
    {
        $updated = $this->service->update($apartment, $request->validated());

        return response()->json(['success' => true, 'message' => 'واحد با موفقیت ویرایش شد.', 'data' => new ApartmentResource($updated)]);
    }

    public function destroy(Request $request, Apartment $apartment): JsonResponse
    {
        $this->authorize('delete', $apartment);
        $this->service->delete($apartment);

        return response()->json(['success' => true, 'message' => 'واحد با موفقیت حذف شد.', 'data' => null]);
    }
}
