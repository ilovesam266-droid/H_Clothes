<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\Order\Resource as OrderResource;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    /**
     * Store a new order (Checkout).
     */
    public function store(StoreOrderRequest $request): OrderResource|JsonResponse
    {
        try {
            $order = $this->checkoutService->checkout(
                userId: (int) $request->user()->id,
                addressId: (int) $request->address_id,
                paymentMethod: $request->payment_method,
                customerNote: $request->customer_note
            );

            // Load items and address for the resource
            $order->load(['items', 'address']);

            return new OrderResource($order);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
