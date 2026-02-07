<?php

namespace App\Http\Resources\Category;

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
            'name' => $this->name,
            'slug' => $this->slug,
            'created_by' => [
                'name' => $this->creator?->user_name,
                'email' => $this->creator?->email,
            ],
            'created_at' => $this->created_at? [
                'date' => $this->created_at->format('d-m-Y'),
                'time' => $this->created_at->format('H:i:s'),
            ] : null,
            'updated_at' => $this->updated_at? [
                'date' => $this->updated_at->format('d-m-Y'),
                'time' => $this->updated_at->format('H:i:s'),
            ] : null,
            'deleted_at' => $this->deleted_at? [
                'date' => $this->deleted_at->format('d-m-Y'),
                'time' => $this->deleted_at->format('H:i:s'),
            ] : null,
        ];
    }
}
