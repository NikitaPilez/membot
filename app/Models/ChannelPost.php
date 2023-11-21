<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChannelPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'description',
        'post_id',
        'publication_at',
        'views',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views' => 'integer',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function stat(): HasOne
    {
        return $this->hasOne(ChannelPostStat::class);
    }
}
