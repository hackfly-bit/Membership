<?php

namespace App\Http\Controllers\API;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Customer\CustomerResource;
use App\Http\Requests\Customer\CreateCustomerRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends Controller
{
    use ApiResponse;
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {

        $cabangId = request()->cabang_id ?? null;
        $customers = Customer::useFilters()
            ->whereHas('cabang', function ($query) use ($cabangId) {
                // if cabang_id is 1, return all data
                if ($cabangId == 1) return;
                $query->where('id', $cabangId);
            })
            ->dynamicPaginate();
        // $customers = Customer::with('transaksi')->useFilters()->dynamicPaginate()->sortByDesc('created_at');

        return CustomerResource::collection($customers);
    }

    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $customer = Customer::create($request->validated());

        return $this->responseCreated('Customer created successfully', new CustomerResource($customer));
    }

    public function show(Customer $customer): JsonResponse
    {
        return $this->responseSuccess(null, new CustomerResource($customer));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $customer->update($request->validated());

        return $this->responseSuccess('Customer updated Successfully', new CustomerResource($customer));
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return $this->responseDeleted();
    }

   
}
