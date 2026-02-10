<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Models\Address;
use App\Repositories\Constracts\AddressRepositoryInterface;

class AddressRepository extends BaseRepository implements AddressRepositoryInterface
{
    public function getModel()
    {
        return Address::class;
    }

    public function deleteAddress($idOrCriteria) {
        if (!is_array($idOrCriteria)) {
            $idOrCriteria = [$idOrCriteria];
        }

        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->delete($criteria);
    }
}
