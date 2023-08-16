<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Http;

class GoogleService
{
    public function getFiles(string $folderId)
    {
        $response = Http::get("https://www.googleapis.com/drive/v3/files", [
            'q' => "'{$folderId}' in parents and trashed = false",
            'key' => config('services.google.api_key')
        ])->json();

        return $response['files'];
    }

    public function downloadFile(array $file)
    {
        $response = Http::get("https://www.googleapis.com/drive/v3/files/" . $file['id'], [
            'alt' => "media",
            'key' => config('services.google.api_key')
        ]);

        file_put_contents(public_path() . '/' . $file['name'], $response->body());

        return Video::create([
            'name' => $file['name'],
            'google_file_id' => $file['id'],
        ]);
    }
}
