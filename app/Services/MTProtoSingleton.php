<?php

namespace App\Services;

use danog\MadelineProto\API;

class MTProtoSingleton
{
    private static API $proto;
    public static function getProtoInstance()
    {
        if (!isset(self::$proto)) {
            self::$proto = new API(storage_path() . "/session");
        }

        self::$proto->start();

        return self::$proto;
    }
}
