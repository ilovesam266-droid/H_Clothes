<?php

namespace App\Services;

use App\Http\Resources\Category\Resource;
use App\Repositories\Eloquents\CategoryRepository;

class CategoryService
{
    protected $categoryRepo;
    public function __construct(CategoryRepository $category)
    {
        $this->categoryRepo = $category;
    }

    public function getAllCategory(){
        $categories = $this->categoryRepo->getAll();

        return Resource::collection($categories);
    }
}
