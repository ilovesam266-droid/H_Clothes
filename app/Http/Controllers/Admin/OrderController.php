<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        return view('admin.pages.orders.index');
    }

    public function show(int $id): \Illuminate\View\View
    {
        return view('admin.pages.orders.show', ['orderId' => $id]);
    }
}
