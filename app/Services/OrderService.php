<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Repositories\Constracts\OrderRepositoryInterface;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository
    ) {}

    /**
     * Get paginated, filtered list of orders.
     */
    public function getAllOrders(Request $request): LengthAwarePaginator
    {
        return $this->orderRepository->getAllOrders($request);
    }

    /**
     * Find a single order with full relations.
     *
     * @throws Exception
     */
    public function getOrderById(int $id): Order
    {
        /** @var Order|null */
        $order = Order::with(['user', 'items', 'address', 'payments'])->find($id);

        if (! $order) {
            throw new Exception('Đơn hàng không tồn tại.');
        }

        return $order;
    }

    /**
     * Update an order's status with optional note / cancel reason.
     *
     * Status flow:
     *  0 Chờ xử lý → 1 Đã xác nhận → 2 Chờ vận chuyển → 3 Đang giao → 4 Hoàn thành
     *                                                                     → 5 Đã hủy
     *                                                                     → 6 Thất bại
     *
     * @throws Exception
     */
    public function updateStatus(int $id, int $status, ?string $adminNote = null, ?string $cancelReason = null): Order
    {
        $data = [
            'status' => $status,
            'admin_note' => $adminNote,
        ];

        if ($status === 5) {
            $data['cancel_reason'] = $cancelReason;
            $data['cancelled_at'] = now();
        } elseif ($status === 6) {
            $data['failed_reason'] = $cancelReason;
            $data['failed_at'] = now();
        } elseif ($status === 1) {
            $data['confirmed_at'] = now();
        } elseif ($status === 4) {
            $data['delivered_at'] = now();
        }

        return $this->orderRepository->updateOrderStatus($id, $data);
    }
}
