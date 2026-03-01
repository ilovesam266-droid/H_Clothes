<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.pages.products.index');
    }

    public function create(): \Illuminate\View\View
    {
        return view('admin.pages.products.create');
    }

    public function edit(int $id): \Illuminate\View\View
    {
        return view('admin.pages.products.edit', ['productId' => $id]);
    }
}
