<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property int $channel_post_id
 * @property int $views
 * @property int $shares
 * @property Carbon $created_at
 * @property ChannelPost $post
 */
class ChannelPostStat extends Model
{
    use HasFactory;

    protected $table = 'channel_post_stats';

    protected $fillable = [
        'channel_post_id',
        'views',
        'shares',
        'created_at',
    ];

    protected $casts = [
        'channel_post_id' => 'integer',
        'shares' => 'integer',
        'views' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(ChannelPost::class, 'channel_post_id');
    }
}
