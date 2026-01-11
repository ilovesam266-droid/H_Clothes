<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/admin')->name('admin.')->group(function(){
    Route::get('/login', [AuthController::class, 'loginShow'])->name('loginShow');
    Route::get('/register', [AuthController::class, 'registerShow'])->name('register');
    Route::get('/forgot', [AuthController::class, 'forgotShow'])->name('forgot');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/dashboard', [DashboardController::class, 'dashboardShow'])->name('dashboard');
});

