<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $variant = $this->variant;
        $product = $variant?->product;

        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'quantity' => $this->quantity,
            'price' => $variant?->price,
            'subtotal' => ($variant?->price ?? 0) * $this->quantity,
            'variant_info' => [
                'size' => $variant?->size,
                'color' => $variant?->color,
                'sku' => $variant?->sku,
            ],
            'product_info' => [
                'name' => $product?->name,
                'slug' => $product?->slug,
                'image' => $product?->images->first()?->url,
            ],
        ];
    }
}
