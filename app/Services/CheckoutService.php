<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Constracts\AddressRepositoryInterface;
use App\Repositories\Constracts\CartRepositoryInterface;
use App\Repositories\Constracts\OrderRepositoryInterface;
use App\Repositories\Constracts\PaymentRepositoryInterface;
use App\Repositories\Constracts\VariantRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository,
        protected OrderRepositoryInterface $orderRepository,
        protected PaymentRepositoryInterface $paymentRepository,
        protected AddressRepositoryInterface $addressRepository,
        protected VariantRepositoryInterface $variantRepository
    ) {}

    /**
     * Process checkout from cart to order.
     *
     * @throws Exception
     */
    public function checkout(int $userId, int $addressId, int $paymentMethod, ?string $customerNote = null): Order
    {
        $cart = $this->cartRepository->findByUser($userId);
        if (! $cart) {
            throw new Exception('Giỏ hàng không tồn tại.');
        }

        $cartWithItems = $this->cartRepository->getCartWithItems($cart->id);
        if ($cartWithItems->items->isEmpty()) {
            throw new Exception('Giỏ hàng đang trống.');
        }

        // 1. Validation: Kiểm tra tồn kho
        foreach ($cartWithItems->items as $item) {
            if ($item->quantity > $item->variant->stock) {
                throw new Exception("Sản phẩm {$item->variant->product->name} ({$item->variant->full_name}) không đủ tồn kho.");
            }
        }

        return DB::transaction(function () use ($userId, $addressId, $paymentMethod, $customerNote, $cartWithItems) {
            // 2. Tính toán total_amount
            $totalAmount = $cartWithItems->items->sum(fn ($item) => $item->quantity * $item->variant->price);

            // 3. Tạo Order
            $order = $this->orderRepository->createOrder([
                'created_by' => $userId,
                'status' => 0, // 0: pending
                'total_amount' => $totalAmount,
                'customer_note' => $customerNote,
            ]);

            // 4. Lưu OrderAddress snapshot
            $address = $this->addressRepository->find($addressId);
            if (! $address || $address->created_by !== $userId) {
                throw new Exception('Địa chỉ không hợp lệ.');
            }

            $user = User::findOrFail($userId);
            $this->orderRepository->createAddressSnapshot($order->id, [
                'recipient_name' => $user->full_name,
                'recipient_phone' => '0000000000', // Mặc định vì database không có trường phone
                'recipient_email' => $user->email,
                'province' => $address->province,
                'district' => $address->district,
                'ward' => $address->ward,
                'address_detail' => $address->detail,
            ]);

            // 5. Lưu OrderItems snapshot & Cập nhật tồn kho
            $orderItems = [];
            foreach ($cartWithItems->items as $item) {
                $orderItems[] = [
                    'variant_id' => $item->variant_id,
                    'product_name' => $item->variant->product->name,
                    'variant_name' => $item->variant->full_name,
                    'unit_price' => $item->variant->price,
                    'quantity' => $item->quantity,
                ];

                // Giảm tồn kho và tăng lượt bán
                $item->variant->decrement('stock', $item->quantity);
                $item->variant->increment('sold', $item->quantity);
            }
            $this->orderRepository->createItems($order->id, $orderItems);

            // 6. Khởi tạo Payment
            $this->paymentRepository->initPayment($order->id, $userId, $totalAmount, $paymentMethod);

            // 7. Xóa giỏ hàng
            $this->cartRepository->clearCart($cartWithItems->id);

            return $order;
        });
    }
}
