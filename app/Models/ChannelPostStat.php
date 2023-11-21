<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property int $channel_post_id
 * @property int|null $views_after_hour
 * @property int|null $views_after_sixth_hour
 * @property int|null $views_after_twelve_hour
 * @property int|null $views_after_day
 * @property Carbon $created_at
 * @property Channel $channel
 */
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
}
