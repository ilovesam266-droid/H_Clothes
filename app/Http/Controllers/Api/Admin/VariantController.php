<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VariantRequest;
use App\Http\Resources\Variant\Resource;
use App\Services\VariantService;

class VariantController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(int $id, VariantRequest $request, VariantService $variantService)
    {
        $data = $request->validated();

        $variant = $variantService->createVariant($id, $data);

        return new Resource($variant);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, VariantRequest $request, VariantService $variantService)
    {
        $data = $request->validated();

        $variant = $variantService->updateVariant($id, $data);

        return new Resource($variant);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, VariantService $variantService)
    {
        $variant = $variantService->deleteVariant($id);

        return $variant;
    }
}
