<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'size',
        'color',
        'sku',
        'price',
        'stock',
        'sold',
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'sold'  => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->size} - {$this->color}";
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }
}
