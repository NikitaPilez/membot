<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'google_file_id',
        'is_sent',
        'url',
        'content_url',
        'type',
        'send_at',
        'comment',
        'publication_date',
        'preview_image_path',
        'is_prod',
    ];
}
