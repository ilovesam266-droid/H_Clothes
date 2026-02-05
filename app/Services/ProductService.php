<?php

namespace App\Services;

use App\Helpers\Repository;
use App\Repositories\Constracts\ProductRepositoryInterface;

class ProductService
{
    protected $productRepo;
    public function __construct(ProductRepositoryInterface $product)
    {
        $this->productRepo = $product;
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
