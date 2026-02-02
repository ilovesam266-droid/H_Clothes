<?php

use App\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('/admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'destroy'])->name('users-delete');
    Route::patch('/users/restore', [UserController::class,'restore'])->name('user-restore');
    Route::get('/users/{id}', [UserController::class,'show'])->name('user-show');
    Route::post('/users/create', [UserController::class,'store'])->name('user-create');
    Route::post('/users/{id}/edit', [UserController::class,'update'])->name('user-edit'); // PHP can't parse
    Route::delete('/users/{id}/delete', [UserController::class,'delete'])->name('user-delete');
});
