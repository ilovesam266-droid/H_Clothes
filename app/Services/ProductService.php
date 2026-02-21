<?php

namespace App\Services;

use App\Helpers\Repository;
use App\Repositories\Constracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    protected $productRepo;
    public function __construct(ProductRepositoryInterface $product)
    {
        $this->productRepo = $product;
    }

    public function createProduct($data, $userId)
    {
        return DB::transaction(function () use ($data, $userId) {

            $productData = $data->only([
                'name',
                'status',
                'description',
                'detail'
            ]);

            $productData['slug'] = Str::slug($productData['name']);
            $productData['created_by'] = $userId;

            $product = $this->productRepo->create($productData);

            if (!$product) {
                throw new \Exception('Create product failed');
            }

            $categories = $data->input('categories', []);

            if (!empty($categories)) {
                $product->categories()->sync($categories);
            }

            $images = $data->input('images', []);

            if (!empty($images)) {

                $pivotData = [];

                foreach ($images as $position => $imageId) {
                    $pivotData[$imageId] = [
                        'position' => $position
                    ];
                }

                $product->images()->sync($pivotData);
            }

            return $product;
        });
    }

    public function getAllProduct($request)
    {
        $products = $this->productRepo->getAll($request);

        return $products;
    }

    public function deleteProduct($idOrCriteria)
    {
        return $this->productRepo->deleteProduct($idOrCriteria);
    }

    public function restoreProduct($idOrCriteria)
    {
        return $this->productRepo->restoreProduct($idOrCriteria);
    }
}
