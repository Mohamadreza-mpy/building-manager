<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChargeRequest;
use App\Http\Requests\UpdateChargeRequest;
use App\Http\Resources\ChargeResource;
use App\Models\Building;
use App\Models\Charge;
use App\Services\ChargeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChargeController extends Controller
{
    public function __construct(private ChargeService $service) {}

    public function index(Request $request, Building $building): AnonymousResourceCollection
    {
        $this->authorize('view', $building);

        return ChargeResource::collection($this->service->list($building))->additional(['success' => true, 'message' => 'فهرست شارژها دریافت شد.']);
    }

    public function myCharges(Request $request): AnonymousResourceCollection
    {
        abort_unless($request->user()->role === 'resident', 403);

        return ChargeResource::collection($this->service->listForResident($request->user()))->additional(['success' => true, 'message' => 'فهرست شارژهای شما دریافت شد.']);
    }

    public function store(StoreChargeRequest $request, Building $building): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'شارژ با موفقیت ایجاد شد.', 'data' => new ChargeResource($this->service->create($building, $request->validated()))], 201);
    }

    public function show(Charge $charge): JsonResponse
    {
        $this->authorize('view', $charge);

        return response()->json(['success' => true, 'message' => 'اطلاعات شارژ دریافت شد.', 'data' => new ChargeResource($this->service->find($charge))]);
    }

    public function update(UpdateChargeRequest $request, Charge $charge): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'شارژ با موفقیت ویرایش شد.', 'data' => new ChargeResource($this->service->update($charge, $request->validated()))]);
    }
}
