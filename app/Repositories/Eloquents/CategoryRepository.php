<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Models\Category;
use App\Repositories\Constracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return Category::class;
    }

    public function getAll($request){
        return $this->all(
            criteria: function ($query) use ($request){
                $query->with('creator', function ($q) {
                    $q->select('id', 'user_name', 'email');
                });

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
                        $subQuery->where('name', '=', $request->search)
                        ->orWhere('slug', '=', $request->search);
                    });
                });
            },
            perPage: $request->perPage ?? 10,
            columns: ['*'],
            pageName: 'CategoryDashboard');
    }

    public function getCategoryById($idOrCriteria) {
        return $this->find($idOrCriteria);
    }

    public function deleteCategory($idOrCriteria)
    {
        if (!is_array($idOrCriteria))
            { $idOrCriteria = [$idOrCriteria]; }

        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->delete($criteria);
    }

    public function restoreCategory($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->restore($criteria);
    }
}
