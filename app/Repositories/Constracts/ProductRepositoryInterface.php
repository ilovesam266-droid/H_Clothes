<?php

namespace App\Repositories\Constracts;

use App\Repositories\Constracts\BaseRepositoryInterface;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll($request);

    public function deleteProduct($idOrCriteria);

    public function restoreProduct($idOrCriteria);
}
