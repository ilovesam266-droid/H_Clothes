<?php

namespace App\Repositories\Constracts;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUser(int $userId);

    public function getCartWithItems(int $cartId);

    public function findItemInCart(int $cartId, int $variantId);

    public function removeItem(int $cartItemId);

    public function clearCart(int $cartId);
}
