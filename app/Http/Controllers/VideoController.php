<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DownloadVideoRequest;
use App\Jobs\DownloadVideoJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;

class VideoController
{
    public function downloadVideo(DownloadVideoRequest $request): RedirectResponse
    {
        $url = (string) $request->input('url');
        $isProd = (boolean) $request->input('is_prod');
        $description = (string) $request->input('description');
        $comment = (string) $request->input('comment');
        $type = (string) $request->input('type');

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->file('video');

        if ($uploadedFile) {
            $uploadedFile->store();
            $url = $uploadedFile->getRealPath();
        }

        DownloadVideoJob::dispatch($url, $type, $isProd, $description, $comment);

        return back();
    }
}
