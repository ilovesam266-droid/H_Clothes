<?php

namespace App\Services;

use App\Repositories\Constracts\AddressRepositoryInterface;
use App\Repositories\Constracts\UserRepositoryInterface;

class AddressService
{
    protected $addressRepo;
    protected $userRepo;
    public function __construct(AddressRepositoryInterface $addressRepo, UserRepositoryInterface $userRepo)
    {
        $this->addressRepo = $addressRepo;
        $this->userRepo = $userRepo;
    }

    public function getAddress($request, $id) {
        $address = $this->userRepo->getUserById($id)->addresses;

        return $address;
    }

    public function storeAddress($data, $id) {
        $data['created_by'] = $id;
        $address = $this->addressRepo->create($data);

        return $address;
    }

    public function getAddressById($id) {
        $address = $this->addressRepo->find($id);

        return $address;
    }

    public function updateAddress($id, $data) {
        $address = $this->addressRepo->update($id, $data);

        return $address;
    }

    public function deleteAddress($idOrCriteria) {
        return $this->addressRepo->deleteAddress($idOrCriteria);
    }
}
