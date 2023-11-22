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
 * @property boolean $is_active
 * @property Carbon $created_at
 */
class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'url',
        'is_active',
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
