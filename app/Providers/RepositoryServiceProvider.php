<?php

namespace App\Providers;

use App\Repositories\Constracts\ProductRepositoryInterface;
use App\Repositories\Constracts\UserRepositoryInterface;
use App\Repositories\Eloquents\ProductRepository;
use App\Repositories\Eloquents\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
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
