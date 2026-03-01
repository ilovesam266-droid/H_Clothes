<?php

namespace App\Services;

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
                'detail',
            ]);

            $productData['slug'] = Str::slug($productData['name']);
            $productData['created_by'] = $userId;

            $product = $this->productRepo->create($productData);

            if (! $product) {
                throw new \Exception('Create product failed');
            }

            $categories = $data->input('categories', []);

            if (! empty($categories)) {
                $product->categories()->sync($categories);
            }

            $images = $data->input('images', []);

            if (! empty($images)) {

                $pivotData = [];

                foreach ($images as $position => $imageId) {
                    $pivotData[$imageId] = [
                        'position' => $position,
                    ];
                }

                $product->images()->sync($pivotData);
            }

            return $product;
        });
    }

    public function showProduct(int $id)
    {
        $product = $this->productRepo->find($id);

        return $product;
    }

    public function updateProduct(int $id, $data): mixed
    {
        return DB::transaction(function () use ($id, $data) {
            $productData = $data->only([
                'name',
                'status',
                'description',
                'detail',
            ]);

            $productData['slug'] = Str::slug($productData['name']);

            $product = $this->productRepo->update($id, $productData);

            if (! $product) {
                throw new \Exception('Update product failed');
            }

            $categories = $data->input('categories', []);
            $product->categories()->sync($categories);

            $images = $data->input('images', []);

            $pivotData = [];
            foreach ($images as $position => $imageId) {
                $pivotData[$imageId] = ['position' => $position];
            }

            $product->images()->sync($pivotData);

            return $product->fresh(['categories', 'images', 'variants', 'creator']);
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

    public function forceDeleteProduct($idOrCriteria)
    {
        return $this->productRepo->forceDeleteProduct($idOrCriteria);
    }
}
