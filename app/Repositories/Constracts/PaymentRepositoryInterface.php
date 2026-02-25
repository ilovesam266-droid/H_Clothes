<?php

declare(strict_types=1);

namespace App\Repositories\Constracts;

use App\Models\Payment;

interface PaymentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Initialize a payment record for an order.
     */
    public function initPayment(int $orderId, int $userId, int|float $amount, int $method): Payment;
}
