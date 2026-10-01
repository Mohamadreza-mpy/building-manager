<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOwnerRequest;
use App\Http\Resources\UserResource;
use App\Services\OwnerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OwnerController extends Controller
{
    public function __construct(private OwnerService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless(in_array($request->user()->role, ['admin', 'manager'], true), 403);

        return UserResource::collection($this->service->list($request->user(), $request->string('search')->toString() ?: null))
            ->additional(['success' => true, 'message' => 'فهرست مالکین دریافت شد.']);
    }

    public function store(StoreOwnerRequest $request): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'مالک با موفقیت ثبت شد.', 'data' => new UserResource($this->service->create($request->user(), $request->validated()))], 201);
    }
}
