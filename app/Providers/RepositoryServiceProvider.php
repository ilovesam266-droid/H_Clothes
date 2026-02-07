<?php

namespace App\Providers;

use App\Repositories\Constracts\CategoryRepositoryInterface;
use App\Repositories\Constracts\ProductRepositoryInterface;
use App\Repositories\Constracts\UserRepositoryInterface;
use App\Repositories\Eloquents\CategoryRepository;
use App\Repositories\Eloquents\ProductRepository;
use App\Repositories\Eloquents\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
