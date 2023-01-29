<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class DriveController extends BaseController
{
    public function getFiles(): JsonResponse
    {
        $response = Http::get("https://www.googleapis.com/drive/v3/files", [
            "key" => config('services.google.api_key'),
            "q" => "'1PDl5VA24ZUHX2vTkm12z4Dc-C6c6bQ_Z' in parents"
        ])->json();

        return response()->json(['data' => $response]);
    }
}
