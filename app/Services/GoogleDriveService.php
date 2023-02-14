<?php

namespace App\Services;

use App\Models\Video;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Str;

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

    /**
     * @param string $folderId
     * @return DriveFile[]
     */
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
     * @param DriveFile $file
     * @return Video
     */
    public function downloadFile(DriveFile $file): Video
    {
        $response = $this->service->files->get($file->getId(), ['alt' => 'media']);

        file_put_contents(public_path() . '/' . $file->getName(), $response->getBody()->getContents());

        return Video::create([
            'name' => $file->getName(),
            'file_id' => $file->getId(),
        ]);
    }

    public function createFile(string $content)
    {
        $fileMetadata = new Drive\DriveFile([
            'parents' => [config('services.google.mem_video_folder_id')],
            'name' => Str::random(24).'.mp4'
        ]);

        return $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'video/mp4',
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);
    }
}
