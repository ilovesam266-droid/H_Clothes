<?php

namespace App\Repositories\Eloquents;

use App\Models\Category;
use App\Repositories\Constracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return Category::class;
    }

    public function getAll(){
        return $this->all(
            criteria: function ($query){
                $query->with('creator', function ($q) {
                    $q->select('id', 'user_name', 'email');
                });
            },
            perPage: 10,
            columns: ['*'],
            pageName: 'Category Dashboard');
    }
}
