<?php

namespace App\Http\Resources\User;

use App\Enums\UserRole;
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
            'id'         => $this->id,

            // basic info
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'full_name'  => $this->fullName,
            'user_name'  => $this->user_name,
            'email'      => $this->email,

            // profile
            'avatar'     => $this->avatar,
            'birthday'   => optional($this->birthday)->format('Y-m-d'),
            'sex'        => $this->sex,

            // system
            'status'     => $this->status->label(),
            'role'       => $this->role ? [
                'name' => $this->role->label(),
                'color' => $this->role->badgeColor(),
            ] : null,

            // verify
            'email_verified' => !is_null($this->email_verified_at),
            'email_verified_at' => $this->email_verified_at?->toDateTimeString(),

            // timestamps
            'created_at' => $this->created_at? [
                'date' => $this->created_at->format('d-m-Y'),
                'time' => $this->created_at->format('H:i:s'),
            ] : null,
            'updated_at' => $this->updated_at? [
                'date' => $this->updated_at->format('d-m-Y'),
                'time' => $this->updated_at->format('H:i:s'),
            ] : null,
        ];
    }
}
