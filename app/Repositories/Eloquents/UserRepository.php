<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Models\User;
use App\Repositories\Constracts\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Create a new class instance.
     */
    public function getModel()
    {
        return User::class;
    }

    public function getAll($request)
    {
        return $this->all(
            criteria: function ($query) use ($request) {
                $query->when(
                    $request->trashed ?? null,
                    function ($q, $trashed) {
                        match ($trashed) {
                            'only' => $q->onlyTrashed(),
                            'with' => $q->withTrashed(),
                            'active' => null,
                        };
                    }
                );

                $query->when(isset($request->search), function ($innerQuery) use ($request) {
                    $innerQuery->where(function ($subQuery) use ($request) {
                        $subQuery->where('user_name', '=', $request->search)
                            ->orWhere('first_name', '=', $request->search)
                            ->orWhere('last_name', '=', $request->search)
                            ->orWhere('email', '=', $request->search);
                    });
                })->when(isset($request->filters['status']), function ($innerQuery) use ($request) {
                    $innerQuery->where('status', '=', $request->filters['status']);
                })->when(isset($request->filters['role']), function ($innerQuery) use ($request) {
                    $innerQuery->where('role', '=', $request->filters['role']);
                })->when(isset($request->filters['verified']), function ($q) use ($request) {
                    match ((int) $request->filters['verified']) {
                        1 => $q->whereNotNull('email_verified_at'),
                        0 => $q->whereNull('email_verified_at'),
                    };
                });
            },
            perPage: $request->perPage ?? 15,
            columns: ['*'],
            pageName: "UserDashboard"
        );
    }

    public function getUserById($idOrCriteria){
        return $this->find($idOrCriteria);
    }

    public function deleteUser($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->delete($criteria);
    }

    public function restoreUser($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->restore($criteria);
    }
}
