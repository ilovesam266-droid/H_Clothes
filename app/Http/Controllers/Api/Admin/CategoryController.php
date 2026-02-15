<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Category\Resource;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, CategoryService $categoryService)
    {
        $categories = $categoryService->getAllCategory($request);

        return Resource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request, CategoryService $categoryService)
    {
        $user = $request->user('sanctum')->id;
        $data = $request->validated();

        $category = $categoryService->storeCategory($data, $user);

        return new Resource($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, CategoryService $categoryService)
    {
        $category = $categoryService->getCategoryById($id);

        return new Resource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, CategoryRequest $request, CategoryService $categoryService)
    {
        $data = $request->validated();

        $category = $categoryService->updateCategory($id, $data);

        return new Resource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, CategoryService $categoryService)
    {
        $ids = $request->all();

        $categories = $categoryService->deleteCategory($ids);

        return response()->json([
            'message' => 'Deleted succesfully',
            'deleted_count' => $categories,
        ]);
    }

    public function delete(int $id, Request $request, CategoryService $categoryService)
    {
        $category = $categoryService->deleteCategory($id);

        return response()->json([
            'message' => 'Deleted succesfully',
            'data' => $category,
        ]);
    }

    public function restore(Request $request, CategoryService $categoryService)
    {
        $ids = $request->all();

        $categories = $categoryService->restoreCategory($ids);

        return response()->json([
            'message' => 'Restored succesfully',
            'restored_count' => $categories,
        ]);
    }
}
