<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected Google_Client $client;

    protected Google_Service_Drive $service;

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
     * @param string $id
     * @return DriveFile
     */
    public function getFileById(string $id): DriveFile
    {
        return $this->service->files->get($id);
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
     */
    public function downloadFile(DriveFile $file): void
    {
        try {
            $response = $this->service->files->get($file->getId(), ['alt' => 'media']);

            file_put_contents(storage_path('app/public') . '/' . $file->getName(), $response->getBody()->getContents());

            Log::channel('content')->info('Файл загружен на сервер.', [
                'fileId' => $file->getId(),
            ]);
        } catch (Exception $exception) {
            Log::channel('content')->error('Произошла ошибка во время загрузки файла на сервер.', [
                'fileId' => $file->getId(),
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function createFile(string $content, string $fileName): DriveFile
    {
        $fileMetadata = new DriveFile([
            'parents' => [config('services.google.mem_video_folder_id')],
            'name' => $fileName,
        ]);

        return $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => 'video/mp4',
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);
    }
}
