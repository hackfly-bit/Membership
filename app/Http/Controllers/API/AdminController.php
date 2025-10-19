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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $user = User::create();

        return $this->responseCreated('User created successfully', new AdminResource($user));
    }

    public function newStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            // 'role' => 'required|string|max:255',
            'password' => 'required|string|min:8',
            'cabang_id' => 'required|integer',
            // 'token_wa' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password,
            'cabang_id' => $request->cabang_id,
            'token_wa' => $request->token_wa ?? 'Belum ada token',
        ]);

        return $this->responseCreated('User created successfully', new AdminResource($user));
    }

    public function show($user): JsonResponse
    {
        $user = User::find($user);
        return $this->responseSuccess(null, new AdminResource($user));
    }

    public function update(UpdateAdminRequest $request, User $userId): JsonResponse
    {
        Log::info(["request" => $request->validated(), "user" => $userId]);
        $userId->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password,
            'cabang_id' => $request->cabang_id,
            'token_wa' => $request->token_wa,
        ]);

        return $this->responseSuccess('User updated Successfully', new AdminResource($userId));
    }

    public function updateNew(Request $request,  $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $id,
            // 'role' => 'required|string|max:255',
            // 'password' => 'required|string|min:8',
            // 'cabang_id' => 'required|integer',
            'token_wa' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }



        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email ?? $user->email,
            'role' => $request->role ?? $user->role,
            'password' => $request->password ?? $user->password,
            'cabang_id' => $request->cabang_id ?? $user->cabang_id,
            'token_wa' => $request->token_wa ?? $user->token_wa,
        ]);
        return $this->responseSuccess('User updated Successfully', new AdminResource($user));

    }

    public function newDestroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }
}
