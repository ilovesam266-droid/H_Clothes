<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Order\Resource as OrderResource;
use App\Services\OrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display a paginated listing of orders.
     */
    public function index(Request $request): ResourceCollection
    {
        $orders = $this->orderService->getAllOrders($request);

        return OrderResource::collection($orders);
    }

    /**
     * Display a single order.
     */
    public function show(int $id): OrderResource|JsonResponse
    {
        try {
            $order = $this->orderService->getOrderById($id);

            return new OrderResource($order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(int $id, Request $request): OrderResource|JsonResponse
    {
        $request->validate([
            'status' => ['required', 'integer', 'between:0,6'],
            'admin_note' => ['nullable', 'string', 'max:500'],
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $order = $this->orderService->updateStatus(
                id: $id,
                status: (int) $request->status,
                adminNote: $request->admin_note,
                cancelReason: $request->cancel_reason,
            );

            return new OrderResource($order);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
