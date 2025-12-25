<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mail extends Model
{
    protected $fillable = [
        'type',
        'subject',
        'body',
        'variables',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function batches()
    {
        return $this->hasMany(MailBatch::class, 'template_id');
    }
}
