<?php

namespace App\Http\Controllers\API;

use App\Models\Template;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Resources\Template\TemplateResource;
use App\Http\Requests\Template\CreateTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TemplateController extends Controller
{
    use ApiResponse;
    public function __construct()
    {

    }

    public function index(): AnonymousResourceCollection
    {
        $templates = Template::useFilters()->dynamicPaginate();

        return TemplateResource::collection($templates);
    }

    public function store(CreateTemplateRequest $request): JsonResponse
    {
        $template = Template::create($request->validated());

        return $this->responseCreated('Template created successfully', new TemplateResource($template));
    }

    public function show(Template $template): JsonResponse
    {
        return $this->responseSuccess(null, new TemplateResource($template));
    }

    public function update(UpdateTemplateRequest $request, Template $template): JsonResponse
    {
        $template->update($request->validated());

        return $this->responseSuccess('Template updated Successfully', new TemplateResource($template));
    }

    public function destroy(Template $template): JsonResponse
    {
        $template->delete();

        return $this->responseDeleted();
    }

}
