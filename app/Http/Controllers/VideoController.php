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
        $isProd = (boolean) $request->input('is_prod');

        DownloadVideoJob::dispatch($url, Utils::getSocialTypeByLink($url), $isProd, $request->input('comment'));

        return back();
    }

    public function downloadContent(Request $request): RedirectResponse
    {
        $isProd = (boolean) $request->input('is_prod');

        DownloadVideoJob::dispatch($request->input('content_url'), 'simple', $isProd);

        return back();
    }
}
