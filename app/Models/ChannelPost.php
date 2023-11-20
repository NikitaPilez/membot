<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'description',
        'post_id',
        'publication_at',
        'views_after_hour',
        'views_after_sixth_hour',
        'views_after_twelve_hour',
        'views_after_day',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views_after_hour' => 'integer',
        'views_after_sixth_hour' => 'integer',
        'views_after_twelve_hour' => 'integer',
        'views_after_day' => 'integer',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
