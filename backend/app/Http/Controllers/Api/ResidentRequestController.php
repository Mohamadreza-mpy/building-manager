<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RespondResidentRequest;
use App\Http\Requests\StoreResidentRequest;
use App\Http\Resources\RequestResource;
use App\Models\ResidentRequest;
use App\Services\ResidentRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ResidentRequestController extends Controller
{
    public function __construct(private ResidentRequestService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ResidentRequest::class);

        return RequestResource::collection($this->service->list($request->user()))->additional(['success' => true, 'message' => 'فهرست درخواست‌ها دریافت شد.']);
    }

    public function store(StoreResidentRequest $request): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'درخواست با موفقیت ثبت شد.', 'data' => new RequestResource($this->service->create($request->user(), $request->validated()))], 201);
    }

    public function update(RespondResidentRequest $request, ResidentRequest $residentRequest): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'پاسخ درخواست با موفقیت ثبت شد.', 'data' => new RequestResource($this->service->respond($residentRequest, $request->validated()))]);
    }
}
