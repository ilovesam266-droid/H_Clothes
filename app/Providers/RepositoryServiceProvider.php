<?php

namespace App\Providers;

use App\Repositories\Constracts\AddressRepositoryInterface;
use App\Repositories\Constracts\CartRepositoryInterface;
use App\Repositories\Constracts\CategoryRepositoryInterface;
use App\Repositories\Constracts\ImageRepositoryInterface;
use App\Repositories\Constracts\OrderRepositoryInterface;
use App\Repositories\Constracts\PaymentRepositoryInterface;
use App\Repositories\Constracts\ProductRepositoryInterface;
use App\Repositories\Constracts\UserRepositoryInterface;
use App\Repositories\Constracts\VariantRepositoryInterface;
use App\Repositories\Eloquents\AddressRepository;
use App\Repositories\Eloquents\CartRepository;
use App\Repositories\Eloquents\CategoryRepository;
use App\Repositories\Eloquents\ImageRepository;
use App\Repositories\Eloquents\OrderRepository;
use App\Repositories\Eloquents\PaymentRepository;
use App\Repositories\Eloquents\ProductRepository;
use App\Repositories\Eloquents\UserRepository;
use App\Repositories\Eloquents\VariantRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        AddressRepositoryInterface::class => AddressRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        ImageRepositoryInterface::class => ImageRepository::class,
        VariantRepositoryInterface::class => VariantRepository::class,
        CartRepositoryInterface::class => CartRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
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
