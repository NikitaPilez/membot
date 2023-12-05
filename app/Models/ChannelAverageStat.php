<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property int $channel_id
 * @property int $hour_count
 * @property int $avg_share
 * @property int $avg_views
 * @property Carbon $created_at
 */
class ChannelAverageStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'hour_count',
        'avg_share',
        'avg_views',
    ];
}
