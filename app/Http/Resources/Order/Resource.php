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
        $status = (int) $this->status;

        return [
            'id' => $this->id,
            'status' => [
                'value' => $status,
                'name' => $this->getStatusName($status),
                'color' => $this->getStatusColor($status),
            ],
            'total_amount' => (int) $this->total_amount,
            'customer_note' => $this->customer_note,
            'admin_note' => $this->admin_note,
            'cancel_reason' => $this->cancel_reason,
            'failed_reason' => $this->failed_reason,
            'cancelled_at' => $this->cancelled_at?->toDateTimeString(),
            'confirmed_at' => $this->confirmed_at?->toDateTimeString(),
            'delivered_at' => $this->delivered_at?->toDateTimeString(),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'full_name' => $this->user->full_name,
                'email' => $this->user->email,
            ]),
            'address' => $this->formatAddress(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($p) => [
                'method' => $p->method,
                'status' => $p->status,
                'amount' => (int) $p->amount,
            ])),
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
            0 => 'Pending',
            1 => 'Confirmed',
            2 => 'Shipping',
            3 => 'Delivering',
            4 => 'Completed',
            5 => 'Cancelled',
            6 => 'Failed',
            default => 'Unknown',
        };
    }

    /**
     * Return a Bootstrap badge color for the status.
     */
    protected function getStatusColor(int $status): string
    {
        return match ($status) {
            0 => 'warning',
            1 => 'info',
            2 => 'primary',
            3 => 'secondary',
            4 => 'success',
            5 => 'danger',
            6 => 'dark',
            default => 'light',
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
