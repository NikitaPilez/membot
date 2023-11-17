<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
