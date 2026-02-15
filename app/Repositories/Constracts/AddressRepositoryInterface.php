<?php

namespace App\Repositories\Constracts;

use App\Repositories\Constracts\BaseRepositoryInterface;

interface AddressRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteAddress($idOrCriteria);

}
