<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Services\CartService;
use Exception;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index()
    {
        $cart = $this->cartService->getAuthCart();

        return new CartResource($cart);
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->addItem($request->variant_id, $request->quantity);

            return response()->json(['message' => 'Đã thêm sản phẩm vào giỏ hàng'], 201);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function updateItem(Request $request, int $cartItemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->updateItem($cartItemId, $request->quantity);

            return response()->json(['message' => 'Đã cập nhật số lượng'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function removeItem(int $cartItemId)
    {
        try {
            $this->cartService->removeItem($cartItemId);

            return response()->json(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Không tìm thấy sản phẩm trong giỏ'], 404);
        }
    }

    public function clearCart()
    {
        $this->cartService->clearCart();

        return response()->json(['message' => 'Đã làm trống giỏ hàng'], 200);
    }
}
