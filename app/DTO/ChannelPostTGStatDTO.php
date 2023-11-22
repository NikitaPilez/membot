<?php

namespace App\DTO;

class ChannelPostTGStatDTO
{
    public function __construct(public int $id, public int $views, public int $shares)
    {
    }
}
