<?php

namespace App\Repositories\Constracts;

use App\Repositories\Constracts\BaseRepositoryInterface;

interface ImageRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll($request);

    public function getImageById($idOrCriteria);

    public function bulkInsert(array $rows);

    public function deleteImage($idOrCriteria);

    public function restoreImage($idOrCriteria);
}
