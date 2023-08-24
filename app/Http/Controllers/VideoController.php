<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\DownloadVideoJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoController
{
    public function downloadVideo(Request $request): RedirectResponse
    {
        $url = (string) $request->input('url');

        $type = collect([
            'instagram.com' => 'instagram',
            'youtube.com/shorts' => 'youtube',
            'tiktok.com' => 'tiktok',
        ])->firstWhere(function ($value, $key) use ($url) {
            return str_contains($url, $key);
        });

        DownloadVideoJob::dispatch($url, $type);

        return back();
    }

    public function downloadContent(Request $request): RedirectResponse
    {
        DownloadVideoJob::dispatch($request->input('content_url'), 'simple');

        return back();
    }
}
