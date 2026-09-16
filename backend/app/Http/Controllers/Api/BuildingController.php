<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBuildingRequest;
use App\Services\BuildingService;
use Illuminate\Http\Request;

class BuildingController extends Controller
{

    public function __construct(
        private BuildingService $service
    ) {}


    public function store(StoreBuildingRequest $request)
    {
        $user = $request->user();


        if (!in_array($user->role, [
            'admin',
            'manager'
        ])) {

            return response()->json([
                'message'=>'You are not allowed'
            ],403);

        }


        $building = $this->service->create(
            $request->validated(),
            $user
        );


        return response()->json([
            'message'=>'Building created successfully',
            'data'=>$building
        ],201);
    }
}
