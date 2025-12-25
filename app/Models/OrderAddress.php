<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $fillable = [
        'order_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'province',
        'district',
        'ward',
        'address_detail',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
