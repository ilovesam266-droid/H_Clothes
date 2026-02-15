<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Http\Resources\ImageResource;
use App\Services\ImageService;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ImageService $imageService)
    {
        $images = $imageService->getAllCategory($request);

        return ImageResource::collection($images);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageRequest $request, ImageService $imageService)
    {
        $data = $request->validated();

        $paths = $imageService->uploadImages($request->file('images'), $request->user('sanctum')->id);

        return response()->json([
            'message' => 'Upload successful',
            'files' => $paths,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, ImageService $imageService)
    {
        $image = $imageService->getImageById($id);

        return new ImageResource($image);
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
    public function destroy(Request $request, ImageService $imageService)
    {
        $ids = $request->all();

        $images = $imageService->deleteImage($ids);
        if ($images) {
            return response()->json([
                'message' => 'Deleted succesfully',
                'deleted_count' => $images,
            ]);
        } else {
            return response()->json([
                'errors' => 'Delete failed'
            ]);
        }
    }

    public function delete(int $id, Request $request, ImageService $imageService)
    {
        $image = $imageService->deleteImage($id);

        return response()->json([
            'message' => 'Deleted succesfully',
            'data' => $image,
        ]);
    }

    public function restore(Request $request, ImageService $imageService)
    {
        $ids = $request->all();

        $images = $imageService->restoreImage($ids);

        return response()->json([
            'message' => 'Restored succesfully',
            'restored_count' => $images,
        ]);
    }
}
