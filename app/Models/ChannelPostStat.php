<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelPostStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_post_id',
        'views',
    ];

    public function channelPost(): BelongsTo
    {
        return $this->belongsTo(ChannelPost::class);
    }
}
