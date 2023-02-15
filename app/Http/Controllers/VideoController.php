<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadVideoJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController
{
    public function downloadVideo(Request $request): JsonResponse
    {
        DownloadVideoJob::dispatch($request->input("url"), $request->input("type"));

        return response()->json(['data' => 'success']);
    }
}
