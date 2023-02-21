<?php

namespace App\Http\Controllers;

use App\Services\MTProtoSingleton;

class TelegramController extends Controller
{
    public function auth()
    {
        $proto = MTProtoSingleton::getProtoInstance();
        $proto->start();
    }
}
