<?php

namespace App\Services;

use Google\Client;
use Google\Service;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Arr;

class GoogleDriveService
{
    protected $client;

    protected $service;

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->refreshToken(config('services.google.refresh_token'));
        $this->service = new Google_Service_Drive($this->client);
    }

    public function download()
    {
        $folderId = config('services.google.mem_video_folder_id');

        $folderFiles = $this->service->files->listFiles(['q' => "'{$folderId}' in parents and trashed = false"]);

        $fileId = Arr::get($folderFiles->files, '0')?->id;

        if (!$fileId) {
            return;
        }

        $fileInfo = $this->service->files->get($fileId, ['fields' => 'webContentLink']);

        return [
            $fileInfo->getWebContentLink(),
            $fileId
        ];
    }

    public function delete(string $fileId)
    {
        $this->service->files->delete($fileId);
    }
}
