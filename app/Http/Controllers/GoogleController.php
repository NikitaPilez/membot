<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;

class GoogleController
{
    public function __construct(protected GoogleDriveService $googleDriveService) {}

    public function download()
    {
        [$downloadLink, $fileId] = $this->googleDriveService->download();

        if (!$fileId) {
            return;
        }

        //todo download and send to tg

        $this->googleDriveService->delete($fileId);
    }
}
