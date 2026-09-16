<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Building;
use App\Models\Expense;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseService $service) {}

    public function index(Request $request, Building $building): AnonymousResourceCollection
    {
        $this->authorize('viewAny', [Expense::class, $building]);

        return ExpenseResource::collection($this->service->list($building))->additional(['success' => true, 'message' => 'فهرست هزینه‌ها دریافت شد.']);
    }

    public function store(StoreExpenseRequest $request, Building $building): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'هزینه با موفقیت ثبت شد.', 'data' => new ExpenseResource($this->service->create($building, $request->validated(), $request->user()))], 201);
    }
}
