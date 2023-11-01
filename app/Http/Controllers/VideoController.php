<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Utils;
use App\Jobs\DownloadVideoJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoController
{
    public function downloadVideo(Request $request): RedirectResponse
    {
        $url = (string) $request->input('url');

        DownloadVideoJob::dispatch($url, Utils::getSocialTypeByLink($url), $request->input('comment'));

        return back();
    }

    public function downloadContent(Request $request): RedirectResponse
    {
        DownloadVideoJob::dispatch($request->input('content_url'), 'simple');

        return back();
    }
}
