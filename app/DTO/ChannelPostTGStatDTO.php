<?php

namespace App\DTO;

class ChannelPostTGStatDTO
{
    public function __construct(public ?int $id = null, public ?int $views = null, public ?int $shares = null, public ?string $createdAt = null, public ?string $description = null)
    {
    }
}
