<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\DownloadVideoRequest;
use App\Jobs\DownloadVideoJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
            Storage::putFileAs('public', $uploadedFile, $uploadedFile->getClientOriginalName());
            $url = config('app.url') . Storage::url('public/' . $uploadedFile->getClientOriginalName());
        }

        DownloadVideoJob::dispatch($url, $type, $isProd, $description, $comment)->onQueue('content');

        return back();
    }

    public function getStatus(): JsonResponse
    {
        return response()->json([
            'status' => Cache::get('video-status'),
        ]);
    }

    public function deleteStatus(): JsonResponse
    {
        return response()->json([
            'status' => Cache::delete('video-status'),
        ]);
    }
}
