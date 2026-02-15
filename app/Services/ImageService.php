<?php

namespace App\Services;

use App\Repositories\Constracts\ImageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ImageService
{
    protected $imageRepo;
    public function __construct(ImageRepositoryInterface $imageRepository)
    {
        $this->imageRepo = $imageRepository;
    }

    public function getAllCategory($request)
    {
        $images = $this->imageRepo->getAll($request);

        return $images;
    }

    public function uploadImages(array $files, int $userId): array
    {
        $now = now();
        $rows = [];
        $paths = [];

        DB::beginTransaction();

        try {
            foreach ($files as $image) {

                $path = $image->store('images', 'public');

                $rows[] = [
                    'url' => 'storage/' . $path,
                    'created_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $paths[] = $path;
            }

            $this->imageRepo->bulkInsert($rows);

            DB::commit();

            return $paths;

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getImageById($id) {
        $image = $this->imageRepo->getImageById($id);

        return $image;
    }

    public function deleteImage($idOrCriteria) {
        return $this->imageRepo->deleteImage($idOrCriteria);
    }

    public function restoreImage($idOrCriteria)
    {
        return $this->imageRepo->restoreImage($idOrCriteria);
    }
}
