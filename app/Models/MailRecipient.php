<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailRecipient extends Model
{
    protected $fillable = [
        'user_id',
        'mail_batch_id',
        'target_type',
        'target_id',
        'email',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(MailBatch::class, 'mail_batch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
