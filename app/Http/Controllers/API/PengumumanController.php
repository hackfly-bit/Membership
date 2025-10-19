<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengumuman\UpdatePengumumanRequest;
use App\Http\Requests\Pengumuman\CreatePengumumanRequest;
use App\Http\Resources\Pengumuman\PengumumanResource;
use App\Models\Pengumuman;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Essa\APIToolKit\Api\ApiResponse;

class PengumumanController extends Controller
{
    use ApiResponse;
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $pengumumen = Pengumuman::all();

        return PengumumanResource::collection($pengumumen);
    }

    public function store(CreatePengumumanRequest $request): JsonResponse
    {
        $pengumuman = Pengumuman::create($request->validated());

        return $this->responseCreated('Pengumuman created successfully', new PengumumanResource($pengumuman));
    }

    public function show(Pengumuman $pengumuman): JsonResponse
    {
        return $this->responseSuccess(null, new PengumumanResource($pengumuman));
    }

    public function update(UpdatePengumumanRequest $request, Pengumuman $pengumuman): JsonResponse
    {
        $pengumuman->update($request->validated());

        return $this->responseSuccess('Pengumuman updated Successfully', new PengumumanResource($pengumuman));
    }

    public function destroy(Pengumuman $pengumuman): JsonResponse
    {
        $pengumuman->delete();

        return $this->responseDeleted();
    }

   
}
