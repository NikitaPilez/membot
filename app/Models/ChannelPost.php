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
 * @property integer $views
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

    public function getAverageCountViewsByTime(string $mode): float
    {
        if ($mode === 'hour') {
            $column = 'views_after_hour';
        } elseif ($mode === 'sixth') {
            $column = 'views_after_sixth_hour';
        } elseif ($mode === 'twelve') {
            $column = 'views_after_twelve_hour';
        } elseif ($mode === 'day') {
            $column = 'views_after_day';
        } else {
            return 0;
        }

        $result = DB::table('channels as c')
            ->select([DB::raw('SUM(cps.' . $column . ') as sum'), DB::raw('COUNT(cps.' . $column . ') as count')])
            ->join('channel_posts as cp', 'cp.channel_id', '=', 'c.id')
            ->join('channel_post_stats as cps', 'cps.channel_post_id', '=', 'cp.id')
            ->where('cp.channel_id', $this->channel_id)
            ->first();

        if ($result->count) {
            return round(($result->sum / $result->count));
        }

        return 0;
    }
}
