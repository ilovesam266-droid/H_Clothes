<?php

namespace App\Repositories\Constracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll($request);

    public function deleteProduct($idOrCriteria);

    public function restoreProduct($idOrCriteria);
}
