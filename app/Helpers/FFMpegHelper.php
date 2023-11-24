<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class FFMpegHelper
{
    public static function compressVideo(string $fileName): void
    {
        $tmpName = 'tmp_' . $fileName;

        Storage::disk('public')->copy($fileName, $tmpName);
        Storage::disk('public')->delete($fileName);

        exec('ffmpeg -i ' . Storage::disk('public')->path($tmpName) . ' -crf 18 -c:a copy ' . Storage::disk('public')->path($fileName));

        Storage::disk('public')->delete($tmpName);
    }
}
