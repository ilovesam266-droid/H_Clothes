<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents;

use App\Models\Payment;
use App\Repositories\Constracts\PaymentRepositoryInterface;

class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getModel(): string
    {
        return Payment::class;
    }

    /**
     * {@inheritDoc}
     */
    public function initPayment(int $orderId, int $userId, int|float $amount, int $method): Payment
    {
        /** @var Payment */
        return $this->create([
            'order_id' => $orderId,
            'user_id' => $userId,
            'total_amount' => $amount,
            'payment_method' => $method,
            'status' => 0,
        ]);
    }
}
