<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'created_by',
        'status',
        'total_amount',
        'admin_note',
        'customer_note',
        'cancel_reason',
        'failed_reason',
        'cancelled_at',
        'failed_at',
        'confirmed_at',
        'delivered_at',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'failed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
