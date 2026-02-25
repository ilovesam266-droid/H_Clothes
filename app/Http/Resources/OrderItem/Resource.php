<?php

declare(strict_types=1);

namespace App\Http\Resources\OrderItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variant_id' => $this->variant_id,
            'product_name' => $this->product_name,
            'variant_name' => $this->variant_name,
            'unit_price' => (int) $this->unit_price,
            'quantity' => (int) $this->quantity,
            'subtotal' => (int) $this->unit_price * $this->quantity,
        ];
    }
}
