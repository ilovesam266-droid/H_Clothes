<?php

declare(strict_types=1);

namespace App\Http\Resources\Order;

use App\Http\Resources\OrderItem\Resource as OrderItemResource;
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
            'status' => [
                'value' => (int) $this->status,
                'name' => $this->getStatusName((int) $this->status),
            ],
            'total_amount' => (int) $this->total_amount,
            'customer_note' => $this->customer_note,
            'admin_note' => $this->admin_note,
            'address' => $this->formatAddress(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }

    /**
     * Return a readable status name.
     */
    protected function getStatusName(int $status): string
    {
        return match ($status) {
            0 => 'Chờ xử lý',
            1 => 'Đã xác nhận',
            2 => 'Chờ vận chuyển',
            3 => 'Đang giao',
            4 => 'Hoàn thành',
            5 => 'Đã hủy',
            6 => 'Thất bại',
            default => 'Không xác định',
        };
    }

    /**
     * Format the order address snapshot.
     */
    protected function formatAddress(): ?array
    {
        $address = $this->address;
        if (! $address) {
            return null;
        }

        return [
            'recipient_name' => $address->recipient_name,
            'recipient_phone' => $address->recipient_phone,
            'recipient_email' => $address->recipient_email,
            'full_address' => sprintf(
                '%s, %s, %s, %s',
                $address->address_detail,
                $address->ward,
                $address->district,
                $address->province
            ),
        ];
    }
}
