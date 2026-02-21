<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Models\Image;
use App\Repositories\Constracts\ImageRepositoryInterface;
use App\Repositories\Eloquents\BaseRepository;

class ImageRepository extends BaseRepository implements ImageRepositoryInterface
{
    public function getModel()
    {
        return Image::class;
    }

    public function getAll($request) {
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
            },
            perPage: $request->perPage ?? 30,
            columns: ['*'],
            pageName: 'ImageDashboard');
    }

    public function getImageById($idOrCriteria) {
        return $this->find($idOrCriteria);
    }

    public function bulkInsert(array $rows)
    {
        return Image::insert($rows);
    }

    public function deleteImage($idOrCriteria)
    {
        if (!is_array($idOrCriteria))
            { $idOrCriteria = [$idOrCriteria]; }

        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->delete($criteria);
    }

    public function restoreImage($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->restore($criteria);
    }
}
