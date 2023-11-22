<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property-read int $id
 * @property int $channel_id
 * @property string|null $description
 * @property int $post_id
 * @property Carbon $publication_at
 * @property Carbon $created_at
 * @property Channel $channel
 * @property Stat $stat
 */
class ChannelPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'description',
        'post_id',
        'publication_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
