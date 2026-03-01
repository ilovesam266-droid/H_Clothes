<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\OrderItem;
use App\Repositories\Constracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

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

    /**
     * {@inheritDoc}
     */
    public function getAllOrders(Request $request): LengthAwarePaginator
    {
        $query = Order::query()->with(['user', 'address', 'items']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', (int) $request->input('status'));
        }

        $query->latest();

        return $query->paginate((int) $request->input('perPage', 15));
    }

    /**
     * {@inheritDoc}
     */
    public function updateOrderStatus(int $id, array $data): Order
    {
        /** @var Order */
        $order = Order::findOrFail($id);
        $order->update($data);

        return $order->fresh(['user', 'items', 'address']);
    }
}
