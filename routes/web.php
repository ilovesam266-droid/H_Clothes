<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'loginShow'])->name('loginShow');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('register');
    Route::get('/forgot', [AuthController::class, 'forgotShow'])->name('forgot');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboardShow'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('user-create');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('user-edit');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products-create');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');

    Route::get('/images', [ImageController::class, 'index'])->name('images');
})->middleware('auth');
