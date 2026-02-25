<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Repositories\Constracts\OrderRepositoryInterface;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getModel(): string
    {
        return Order::class;
    }

    /**
     * {@inheritDoc}
     */
    public function createOrder(array $data): Order
    {
        /** @var Order */
        return $this->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function createItems(int $orderId, array $items): void
    {
        $formattedItems = array_map(function (array $item) use ($orderId) {
            $item['order_id'] = $orderId;
            $item['created_at'] = now();
            $item['updated_at'] = now();

            return $item;
        }, $items);

        OrderItem::insert($formattedItems);
    }

    /**
     * {@inheritDoc}
     */
    public function createAddressSnapshot(int $orderId, array $addressData): void
    {
        $addressData['order_id'] = $orderId;

        OrderAddress::create($addressData);
    }
}
