<?php

declare(strict_types=1);

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'name',
        'google_file_id',
        'is_sent',
        'url',
        'content_url',
        'type',
        'send_at'
    ];
}
