<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminController extends Controller
{
    use ApiResponse;
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $user = User::useFilters()->dynamicPaginate();

        return AdminResource::collection($user);
    }

    public function store(CreateAdminRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return $this->responseCreated('User created successfully', new AdminResource($user));
    }

    public function show($user): JsonResponse
    {
        $user = User::find($user);
        return $this->responseSuccess(null, new AdminResource($user));
    }

    public function update(UpdateAdminRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return $this->responseSuccess('User updated Successfully', new AdminResource($user));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return $this->responseDeleted();
    }

   
}
