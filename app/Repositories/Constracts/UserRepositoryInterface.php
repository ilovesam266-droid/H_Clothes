<?php

namespace App\Repositories\Constracts;

use App\Repositories\Constracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll($request);

    public function getUserById($idOrCriteria);

    public function deleteUser($idOrCriteria);

    public function restoreUser($idOrCriteria);
}
