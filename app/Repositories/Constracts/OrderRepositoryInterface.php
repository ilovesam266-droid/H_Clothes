<?php

declare(strict_types=1);

namespace App\Repositories\Constracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

interface OrderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new order record.
     *
     * @param  array<string, mixed>  $data
     */
    public function createOrder(array $data): Order;

    /**
     * Create multiple order items for an order.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    public function createItems(int $orderId, array $items): void;

    /**
     * Create an address snapshot for an order.
     *
     * @param  array<string, mixed>  $addressData
     */
    public function createAddressSnapshot(int $orderId, array $addressData): void;

    /**
     * Get paginated list of orders with optional filters.
     */
    public function getAllOrders(Request $request): LengthAwarePaginator;

    /**
     * Update the status and extra fields of an order.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateOrderStatus(int $id, array $data): Order;
}
