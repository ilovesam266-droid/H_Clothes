<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'province',
        'district',
        'ward',
        'detail',
        'is_default',
    ];

    protected $casts = [ 'is_default' => 'boolean', ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
