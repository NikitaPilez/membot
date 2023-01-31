<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\JsonResponse;

class GoogleController
{
    public function __construct(protected GoogleDriveService $googleDriveService) {}

    public function download(): JsonResponse
    {
        /** @var DriveFile[] $files */
        $files = $this->googleDriveService->getFiles(config('services.google.mem_video_folder_id'));
        $this->googleDriveService->downloadFiles($files);
//        $this->googleDriveService->deleteFiles($files);
        return response()->json(['data' => true]);
    }
}
