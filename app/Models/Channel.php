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
 * @property string|null $parse_new_video_link
 * @property boolean $is_active
 * @property string $tgstat_link
 * @property Carbon $created_at
 */
class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'url',
        'parse_new_video_link',
        'is_active',
        'tgstat_link',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(ChannelPost::class);
    }

    public function getChannelAlias(): string
    {
        $splitBySlashArray = explode('/', $this->url);

        return end($splitBySlashArray);
    }
}
