<?php

namespace App\Services;

use App\Repositories\Constracts\UserRepositoryInterface;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->userRepo = $user;
    }

    public function getAllUser($request)
    {
        $users = $this->userRepo->getAll($request);

        return $users;
    }

    public function getUserById($id)
    {
        $user = $this->userRepo->getUserById($id);

        return $user;
    }

    public function storeUser($request, $avatarPath)
    {
        if ($avatarPath) {
            $request['avatar'] = 'storage/'.$avatarPath;
        }

        $user = $this->userRepo->create($request);

        return $user;
    }

    public function updateUser($id, $request, $avatarPath)
    {
        if ($avatarPath) {
            $request['avatar'] = 'storage/'.$avatarPath;
        }

        $user = $this->userRepo->update($id, $request);

        return $user;
    }

    public function deleteUser($idOrCriteria)
    {
        return $this->userRepo->deleteUser($idOrCriteria);
    }

    public function restoreUser($idOrCriteria)
    {
        return $this->userRepo->restoreUser($idOrCriteria);
    }

    public function forceDeleteUser($idOrCriteria)
    {
        return $this->userRepo->forceDeleteUser($idOrCriteria);
    }
}
