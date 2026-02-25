<?php

namespace App\Repositories\Eloquents;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Constracts\CartRepositoryInterface;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function getModel(): string
    {
        return Cart::class;
    }

    public function findByUser(int $userId)
    {
        return $this->model->where('created_by', $userId)->first();
    }

    public function getCartWithItems(int $cartId)
    {
        return $this->model->with(['items.variant.product'])->find($cartId);
    }

    public function findItemInCart(int $cartId, int $variantId)
    {
        return CartItem::where('cart_id', $cartId)
            ->where('variant_id', $variantId)
            ->first();
    }

    public function removeItem(int $cartItemId)
    {
        return CartItem::where('id', $cartItemId)->delete();
    }

    public function clearCart(int $cartId)
    {
        return CartItem::where('cart_id', $cartId)->delete();
    }
}
