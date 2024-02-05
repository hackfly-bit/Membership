<?php

namespace App\Http\Controllers\API;

use App\Filters\CabangFilters;
use App\Models\Cabang;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Cabang\CabangResource;
use App\Http\Requests\Cabang\CreateCabangRequest;
use App\Http\Requests\Cabang\UpdateCabangRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CabangController extends Controller
{
    use ApiResponse;
    public function __construct()
    {
    }

    public function index(): AnonymousResourceCollection
    {   
        $cabangId = request()->cabang_id ?? null;

        if($cabangId == 1){
            $cabangs = Cabang::useFilters()->dynamicPaginate();
        }else{
            $cabangs = Cabang::where('id', $cabangId)->useFilters()->dynamicPaginate();
        }
      

        return CabangResource::collection($cabangs);
    }

    public function store(CreateCabangRequest $request): JsonResponse
    {
        $cabang = Cabang::create($request->validated());

        return $this->responseCreated('Cabang created successfully', new CabangResource($cabang));
    }

    public function show(Cabang $cabang): JsonResponse
    {
        return $this->responseSuccess(null, new CabangResource($cabang));
    }

    public function update(UpdateCabangRequest $request, Cabang $cabang): JsonResponse
    {
        try {
            $cabang->update($request->validated());

            // if success, return success response if error return error response
            return $this->responseSuccess('Cabang updated Successfully', new CabangResource($cabang));
        } catch (\Exception $exception) {
            // Handle other exceptions
            $errorTitle = 'Error';
            $errorDetails = 'An unexpected error occurred.';
            $statusCode = 500; // Internal Server Error

            // Return response with custom error details
            return $this->responseWithCustomError($errorTitle, $errorDetails, $statusCode);
        }
    }

    public function destroy(Cabang $cabang): JsonResponse
    {
        $cabang->delete();

        return $this->responseDeleted();
    }
}
