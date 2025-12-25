<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailBatch extends Model
{
    protected $fillable = [
        'template_id',
        'trigger_type',
        'trigger_source',
        'variables',
        'total_recipients',
        'sent_count',
        'failed_count',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function template()
    {
        return $this->belongsTo(Mail::class, 'template_id');
    }

    public function recipients()
    {
        return $this->hasMany(MailRecipient::class);
    }
}
