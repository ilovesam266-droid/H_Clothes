<?php

namespace App\Services;

use App\Models\Variant;
use App\Repositories\Constracts\CartRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository
    ) {}

    public function getAuthCart()
    {
        $userId = Auth::id();
        $cart = $this->cartRepository->findByUser($userId);

        if (! $cart) {
            $cart = $this->cartRepository->create([
                'created_by' => $userId,
                'status' => 1,
            ]);
        }

        return $this->cartRepository->getCartWithItems($cart->id);
    }

    public function addItem(int $variantId, int $quantity)
    {
        $variant = Variant::findOrFail($variantId);

        if ($variant->stock < $quantity) {
            throw new Exception("Sản phẩm không đủ hàng (Tồn: {$variant->stock})");
        }

        $cart = $this->getAuthCart();
        $existingItem = $this->cartRepository->findItemInCart($cart->id, $variantId);

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $quantity;
            if ($variant->stock < $newQuantity) {
                throw new Exception("Tổng số lượng trong giỏ hàng vượt quá tồn kho (Tồn: {$variant->stock})");
            }
            $existingItem->update(['quantity' => $newQuantity]);

            return $existingItem;
        }

        return $cart->items()->create([
            'variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateItem(int $cartItemId, int $quantity)
    {
        $cart = $this->getAuthCart();
        $item = $cart->items()->findOrFail($cartItemId);
        $variant = $item->variant;

        if ($variant->stock < $quantity) {
            throw new Exception("Sản phẩm không đủ hàng (Tồn: {$variant->stock})");
        }

        $item->update(['quantity' => $quantity]);

        return $item;
    }

    public function removeItem(int $cartItemId)
    {
        $cart = $this->getAuthCart();
        $item = $cart->items()->findOrFail($cartItemId);

        return $item->delete();
    }

    public function clearCart()
    {
        $cart = $this->getAuthCart();

        return $this->cartRepository->clearCart($cart->id);
    }
}
