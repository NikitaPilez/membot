<?php

declare(strict_types=1);

namespace App\Services;

use danog\MadelineProto\API;

class MTProtoSingleton
{
    private static API $proto;
    public static function getProtoInstance()
    {
        if (!isset(self::$proto)) {
            self::$proto = new API(storage_path() . "/framework/sessions/session");
        }

        self::$proto->start();

        return self::$proto;
    }
}
