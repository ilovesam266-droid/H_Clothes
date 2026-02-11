<?php

namespace App\Repositories\Constracts;

interface AddressRepositoryInterface extends BaseRepositoryInterface
{
    public function deleteAddress($idOrCriteria);

}
