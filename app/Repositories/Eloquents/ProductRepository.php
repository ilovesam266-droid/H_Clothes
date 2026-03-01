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
                $query->with(['variants' => function ($q) {
                    $q->select('id', 'product_id', 'stock', 'price', 'sku');
                }]);

                $query->with(['creator' => function ($q) {
                    $q->select('id', 'first_name', 'last_name', 'email');
                }]);

                $query->with(['images' => function ($q) {
                    $q->select('images.id', 'images.url')->withPivot('position');
                }]);

                $query->with(['categories' => function ($q) {
                    $q->select('categories.id', 'categories.name', 'categories.slug');
                }]);

                $query->when(
                    $request->trashed ?? null,
                    function ($q, $trashed) {
                        return match ($trashed) {
                            'only' => $q->onlyTrashed(),
                            'with' => $q->withTrashed(),
                            default => $q,
                        };
                    }
                );

                $query->when($request->search, function ($q, $search) {
                    $q->where(function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%");
                    });
                });

                $query->when($request->filters['status'] ?? null, function ($q, $status) {
                    $q->where('status', $status);
                });

                $query->when($request->filters['creator'] ?? null, function ($q, $creatorId) {
                    $q->where('created_by', $creatorId);
                });

                $query->when($request->filters['stock'] ?? null, function ($q, $stockType) {
                    $q->whereHas('variants', function ($inner) use ($stockType) {
                        match ($stockType) {
                            'in_stock' => $inner->where('stock', '>', 10),
                            'low_stock' => $inner->whereBetween('stock', [1, 10]),
                            'out_of_stock' => $inner->where('stock', '<=', 0),
                            default => null
                        };
                    });
                });
            },
            perPage: $request->perPage ?? 20,
            columns: ['*'],
            pageName: 'ProductDashboard'
        );
    }

    public function deleteProduct($idOrCriteria)
    {
        if (! is_array($idOrCriteria)) {
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

    public function forceDeleteProduct($idOrCriteria)
    {
        $criteria = ['whereIn' => Repository::wrapVlue('id', $idOrCriteria)];

        return $this->forceDelete($criteria);
    }
}
