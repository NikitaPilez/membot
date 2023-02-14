<?php

namespace App\Http\Controllers;

use App\Services\InstagramDownloadService;
use App\Services\TiktokDownloadService;
use App\Services\YoutubeDownloadService;
use Illuminate\Http\Request;

class VideoController
{
    public function downloadVideo(
        Request $request,
        InstagramDownloadService $instagramDownloadService,
        TiktokDownloadService $tiktokDownloadService,
        YoutubeDownloadService $youtubeDownloadService
    ) {
        $url = $request->input('url');

        match ($request->input('type')) {
            'tiktok' => $tiktokDownloadService->download($url),
            'instagram' => $instagramDownloadService->download($url),
            'shorts' => $youtubeDownloadService->shortsDownload($url),
        };
    }
}
