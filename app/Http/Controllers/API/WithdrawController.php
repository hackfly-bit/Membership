<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Withdraw\UpdateWithdrawRequest;
use App\Http\Requests\Withdraw\CreateWithdrawRequest;
use App\Http\Resources\Withdraw\WithdrawResource;
use App\Models\Withdraw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WithdrawController extends Controller
{
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $withdraws = Withdraw::useFilters()->dynamicPaginate();

        return WithdrawResource::collection($withdraws);
    }

    public function store(CreateWithdrawRequest $request): JsonResponse
    {
        $withdraw = Withdraw::create($request->validated());

        return $this->responseCreated('Withdraw created successfully', new WithdrawResource($withdraw));
    }

    public function show(Withdraw $withdraw): JsonResponse
    {
        return $this->responseSuccess(null, new WithdrawResource($withdraw));
    }

    public function update(UpdateWithdrawRequest $request, Withdraw $withdraw): JsonResponse
    {
        $withdraw->update($request->validated());

        return $this->responseSuccess('Withdraw updated Successfully', new WithdrawResource($withdraw));
    }

    public function destroy(Withdraw $withdraw): JsonResponse
    {
        $withdraw->delete();

        return $this->responseDeleted();
    }

   
}
