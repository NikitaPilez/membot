<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property-read int $id
 * @property string $name
 * @property string $google_file_id
 * @property boolean|null $is_sent
 * @property string|null $url
 * @property string|null $content_url
 * @property string|null $type
 * @property Carbon|null $sent_at
 * @property string|null $comment
 * @property Carbon|null $publication_date
 * @property string|null $preview_image_path
 * @property boolean|null $is_prod
 * @property string|null $description
 * @property boolean $is_active
 * @property Carbon $created_at
 */
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
        'description',
    ];

    protected $casts = [
        'publication_date' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
