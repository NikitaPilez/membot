<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelPostStat extends Model
{
    use HasFactory;

    protected $table = 'channel_post_stats';

    protected $fillable = [
        'channel_post_id',
        'views_after_hour',
        'views_after_sixth_hour',
        'views_after_twelve_hour',
        'views_after_day',
    ];

    protected $casts = [
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
