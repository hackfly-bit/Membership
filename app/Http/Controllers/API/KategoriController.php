<?php

namespace App\Http\Controllers\API;

use App\Models\Kategori;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Kategori\KategoriResource;
use App\Http\Requests\Kategori\CreateKategoriRequest;
use App\Http\Requests\Kategori\UpdateKategoriRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KategoriController extends Controller
{
    use ApiResponse;
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $kategoris = Kategori::useFilters()->dynamicPaginate();

        return KategoriResource::collection($kategoris);
    }

    public function store(CreateKategoriRequest $request): JsonResponse
    {
        $kategori = Kategori::create($request->validated());

        return $this->responseCreated('Kategori created successfully', new KategoriResource($kategori));
    }

    public function show(Kategori $kategori): JsonResponse
    {
        return $this->responseSuccess(null, new KategoriResource($kategori));
    }

    public function update(UpdateKategoriRequest $request, Kategori $kategori): JsonResponse
    {
        $kategori->update($request->validated());

        return $this->responseSuccess('Kategori updated Successfully', new KategoriResource($kategori));
    }

    public function destroy(Kategori $kategori): JsonResponse
    {
        $kategori->delete();

        return $this->responseDeleted();
    }

   
}
