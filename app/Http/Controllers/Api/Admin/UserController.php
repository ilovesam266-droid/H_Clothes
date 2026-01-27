<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\User\Resource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserService $userService)
    {
        $users = $userService->getAllUser($request);

        return Resource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request, UserService $userService)
    {
        $user = $userService->storeUser($request->all());

        return new Resource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, UserService $userService)
    {
        $user = $userService->getUserById($id);

        return new Resource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, UserService $userService)
    {
        $ids = $request->all();

        $users = $userService->deleteUser($ids);

        return response()->json([
            'message' => 'Deleted succesfully',
            'deleted_count' => $users,
        ]);
    }

    public function restore(Request $request, UserService $userService)
    {
        $ids = $request->all();

        $users = $userService->restoreUser($ids);

        return response()->json([
            'message' => 'Restored succesfully',
            'restored_count' => $users,
        ]);
    }
}
