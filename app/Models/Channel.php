<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property string|null $name
 * @property string $type
 * @property string $url
 * @property string|null $youtube_id
 * @property string|null $parse_new_video_link
 * @property boolean $is_active
 * @property boolean $is_notify
 * @property string|null $tgstat_link
 * @property Carbon $created_at
 */
class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'url',
        'youtube_id',
        'parse_new_video_link',
        'is_active',
        'is_notify',
        'tgstat_link',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(ChannelPost::class);
    }
}
