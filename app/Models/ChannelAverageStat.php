<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
