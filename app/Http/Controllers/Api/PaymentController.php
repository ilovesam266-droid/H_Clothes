<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Payment\Resource as PaymentResource;
use App\Repositories\Constracts\PaymentRepositoryInterface;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentRepositoryInterface $paymentRepository
    ) {}

    /**
     * Display payment details for an order.
     */
    public function show(int $orderId): PaymentResource|JsonResponse
    {
        $payment = $this->paymentRepository->first([
            'order_id' => $orderId,
        ]);

        if (! $payment) {
            return response()->json([
                'message' => 'Không tìm thấy thông tin thanh toán cho đơn hàng này.',
            ], 404);
        }

        return new PaymentResource($payment);
    }
}
