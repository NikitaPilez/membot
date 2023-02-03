<?php

namespace App\Services;

use App\Models\Video;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Client;
use Google_Service_Drive;

class GoogleDriveService
{
    protected $client;

    protected $service;

    public function __construct() {
        $this->client = new Google_Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->refreshToken(config('services.google.refresh_token'));
        $this->client->addScope(Drive::DRIVE);
        $this->service = new Google_Service_Drive($this->client);
    }

    public function getFiles(string $folderId): array
    {
        $folderFiles = $this->service->files->listFiles(['q' => "'{$folderId}' in parents and trashed = false"]);
        return $folderFiles->getFiles();
    }

    /**
     * @param DriveFile[] $files
     * @return void
     */
    public function deleteFiles(array $files): void
    {
        foreach ($files as $file) {
            $this->service->files->delete($file->getId());
        }
    }

    /**
     * @param DriveFile[] $files
     * @return void
     */
    public function downloadFiles(array $files): void
    {
        $videoIds = Video::where('is_sent', 0)->pluck('file_id');

        foreach ($files as $file) {
            if (!in_array($file->getId(), $videoIds)) {
                $response = $this->service->files->get($file->getId(), array(
                    'alt' => 'media'));
                file_put_contents(public_path() . '/' . $file->getName(), $response->getBody()->getContents());

                Video::create([
                    'name' => $file->getName(),
                    'file_id' => $file->getId(),
                ]);
            }
        }
    }
}
