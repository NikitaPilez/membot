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
        $files = $this->googleDriveService->getFiles("1e5GiW8Kzh3dmLnsOArp1U6miVCaeYhrD");
        $this->googleDriveService->downloadFiles($files);
//        $this->googleDriveService->deleteFiles($files);
        return response()->json(['data' => true]);
    }
}
