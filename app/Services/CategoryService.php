<?php

namespace App\Services;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\Category\Resource;
use App\Repositories\Eloquents\CategoryRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

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

    public function storeCategory($data, $user) {
        $data['slug'] = Str::slug($data['name']);
        $data['created_by'] = $user;

        $category = $this->categoryRepo->create($data);

        return new Resource($category);
    }

    public function updateCategory($id, $data)
    {
        $data['slug'] = Str::slug($data['name']);

        $category = $this->categoryRepo->update($id, $data);

        return $category;
    }

    public function getCategoryById($id) {
        $category = $this->categoryRepo->getCategoryById($id);

        return new Resource($category);
    }

    public function deleteCategory($idOrCriteria) {
        return $this->categoryRepo->deleteCategory($idOrCriteria);
    }

    public function restoreCategory($idOrCriteria)
    {
        return $this->categoryRepo->restoreCategory($idOrCriteria);
    }
}
