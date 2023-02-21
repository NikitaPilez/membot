<?php

namespace App\Http\Controllers;

use App\Jobs\DownloadVideoJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VideoController
{
    public function downloadVideo(Request $request): JsonResponse
    {
        $url = $request->input("url");

        $type = collect([
            'instagram.com' => 'instagram',
            'youtube.com/shorts' => 'youtube',
            'tiktok.com' => 'tiktok',
        ])->firstWhere(function ($value, $key) use ($url) {
            return str_contains($url, $key);
        });

        DownloadVideoJob::dispatch($url, $type);

        return response()->json(['data' => 'success']);
    }
}