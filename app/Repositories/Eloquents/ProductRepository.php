<?php

namespace App\Repositories\Eloquents;

use App\Helpers\Repository;
use App\Models\Product;
use App\Repositories\Constracts\ProductRepositoryInterface;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function getModel()
    {
        return Product::class;
    }

    public function getAll($request)
    {
        return $this->all(
            criteria: function ($query) use ($request) {
                $query->with('variants', function ($q) {
                    $q->select('id', 'product_id', 'stock', 'price', 'sku');
                });

                $query->with('creator', function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'email');
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

                $query->when(
                    isset($request->search),
                    function ($innerQuery) use ($request) {
                        $innerQuery->where(function ($subQuery) use ($request) {
                            $subQuery->where('name', 'like', '%' . $request->search . '%')
                                ->orWhere('slug', 'like', '%' . $request->search . '%');
                        })->when(isset($request->filters['status']), function ($innerQuery) use ($request) {
                            $innerQuery->where('status', '=', $request->filters['status']);
                        });
                    }
                );
            },
            perPage: $request->perPage ?? 20,
            columns: ['*'],
            pageName: 'Products'
        );
    }

    public function deleteProduct($idOrCriteria)
    {
        if (!is_array($idOrCriteria)) {
            $idOrCriteria = [$idOrCriteria];
        }

        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->delete($criteria);
    }

    public function restoreProduct($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->restore($criteria);
    }
}
