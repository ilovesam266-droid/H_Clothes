<?php

declare(strict_types=1);

namespace App\Http\Resources\Payment;

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
            'order_id' => $this->order_id,
            'payment_method' => $this->payment_method,
            'transaction_id' => $this->payment_transaction,
            'amount' => (int) $this->total_amount,
            'status' => [
                'value' => (int) $this->status,
                'name' => $this->getStatusName((int) $this->status),
            ],
            'meta_data' => $this->meta_data,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }

    /**
     * Return a readable status name.
     */
    protected function getStatusName(int $status): string
    {
        return match ($status) {
            0 => 'Chờ thanh toán',
            1 => 'Thành công',
            2 => 'Thất bại',
            3 => 'Đã hoàn tiền',
            default => 'Không xác định',
        };
    }
}
