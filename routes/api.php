<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Api\Admin\AddressController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\ImageController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->name('api.admin.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'destroy'])->name('users-del');
    Route::patch('/users/restore', [UserController::class, 'restore'])->name('user-restore');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('user-show');
    Route::post('/users/create', [UserController::class, 'store'])->name('user-create');
    Route::post('/users/{id}/edit', [UserController::class, 'update'])->name('user-edit'); // PHP can't parse
    Route::delete('/users/{id}/delete', [UserController::class, 'delete'])->name('user-del');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::post('/products', [ProductController::class, 'destroy'])->name('products-del');
    Route::patch('/products/restore', [ProductController::class, 'restore'])->name('products-restore');
    Route::post('/products/create', [ProductController::class, 'store'])->name('products-store');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [CategoryController::class, 'destroy'])->name('categories-del');
    Route::patch('/categories', [CategoryController::class, 'restore'])->name('categories-restore');
    Route::post('/categories/create', [CategoryController::class, 'store'])->name('category-create');
    Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('category-show');
    Route::post('/categories/{id}/edit', [CategoryController::class, 'update'])->name('category-edit');
    Route::delete('/categories/{id}/delete', [CategoryController::class, 'delete'])->name('category-del');

    Route::get('/users/{id}/addresses', [AddressController::class, 'index'])->name('addresses');
    Route::post('/users/{id}/addresses/create', [AddressController::class, 'store'])->name('addresses-create');
    Route::get('/addresses/{id}', [AddressController::class, 'show'])->name('addresses-show');
    Route::post('/addresses/{id}', [AddressController::class, 'update'])->name('address-edit');
    Route::post('/addresses', [AddressController::class, 'destroy'])->name('address-delete');

    Route::get('/images', [ImageController::class, 'index'])->name('images');
    Route::get('/images/{id}', [ImageController::class, 'show'])->name('image-show');
    Route::post('/images', [ImageController::class, 'destroy'])->name('images-del');
    Route::patch('/images', [ImageController::class, 'restore'])->name('images-restore');
    Route::post('/images/upload', [ImageController::class, 'store'])->name('images-upload');
})->middleware('auth:sanctum');
