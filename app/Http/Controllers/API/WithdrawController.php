<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Withdraw\UpdateWithdrawRequest;
use App\Http\Requests\Withdraw\CreateWithdrawRequest;
use App\Http\Resources\Withdraw\WithdrawResource;
use Essa\APIToolKit\Api\ApiResponse;
use App\Models\Withdraw;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;


class WithdrawController extends Controller
{
    use ApiResponse;
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
        $phone = $withdraw->customer->nohp;
        $setting = Template::where('setting_template', 'transaksi')->first();
        $message = $setting->id;
        $token =  Auth::user()->token_wa;

        if($token != null){
            $sendMessage  = (new BroadcastController)->sendMessageAction($request->customer_id,$phone, $message, $token);
        }
    
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

    public function getWithdrawByCustomer($id): AnonymousResourceCollection
    {
        $withdraw = Withdraw::where('customer_id', $id)->get();
        return  WithdrawResource::collection($withdraw);
    }
    
    

   
}
