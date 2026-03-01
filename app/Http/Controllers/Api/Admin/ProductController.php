<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product\Resource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ProductService $productService)
    {
        $products = $productService->getAllProduct($request);

        return Resource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request, ProductService $productService)
    {
        $user = $request->user('sanctum')->id;
        $product = $productService->createProduct($request, $user);

        return new Resource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, ProductService $productService)
    {
        $product = $productService->showProduct($id);

        return new Resource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, ProductRequest $request, ProductService $productService)
    {
        $product = $productService->updateProduct($id, $request);

        return new Resource($product);
    }

    public function updateCategorieImage(int $id, Request $request, ProductService $productService)
    {
        $product = $productService->updateCategorieImage($id, $request);

        return new Resource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ProductService $productService)
    {
        $ids = $request->all();

        $products = $productService->deleteProduct($ids);

        return response()->json([
            'message' => 'Deleted succesfully',
            'deleted_count' => $products,
        ]);
    }

    public function restore(Request $request, ProductService $productService)
    {
        $ids = $request->all();

        $products = $productService->restoreProduct($ids);

        return response()->json([
            'message' => 'Restored succesfully',
            'restored_count' => $products,
        ]);
    }

    public function forceDelete(Request $request, ProductService $productService)
    {
        $ids = $request->all();

        $products = $productService->forceDeleteProduct($ids);

        return response()->json([
            'message' => 'Permanently deleted succesfully',
            'deleted_count' => $products,
        ]);
    }
}
