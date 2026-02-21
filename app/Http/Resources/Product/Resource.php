<?php

namespace App\Http\Resources\Product;

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
            'created_by' => $this->created_by,
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
            'description' => $this->description,
            'detail' => $this->detail, // JSON field
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
            'creator' => [
                'name' => $this->creator?->full_name,
                'email' => $this->creator?->email,
            ],

            'images' => $this->images->map(function ($image) {
                return [
                    'name'       => $image->name,
                    'url'        => $image->url,
                    'position' => $image->pivot->position ?? 0,
                ];
            }),

            'categories' => $this->categories->map(function ($category) {
                return [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ];
            }),

            // Aggregated info từ variants
            'total_variants' => $this->variants->count(),
            'total_stock' => $this->variants->sum('stock'),
            'total_sold' => $this->variants->sum('sold'),
            'min_price' => $this->variants->min('price'),
            'max_price' => $this->variants->max('price'), // Variants

            'variants' => $this->whenLoaded('variants'),
        ];
    }
}
