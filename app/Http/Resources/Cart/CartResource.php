<?php

namespace App\Http\Resources\Cart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $totalItems = $this->items->sum('quantity');
        $totalAmount = $this->items->reduce(function ($carry, $item) {
            return $carry + ($item->variant->price * $item->quantity);
        }, 0);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'summary' => [
                'total_items' => $totalItems,
                'total_unique_items' => $this->items->count(),
                'total_amount' => $totalAmount,
            ],
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
