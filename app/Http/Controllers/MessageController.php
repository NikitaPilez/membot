<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram\SendVideo;
use App\Models\Video;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class MessageController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function processedVideos(): JsonResponse
    {
        $videos = Video::where('is_sent', 0)->get();
        foreach ($videos as $video) {
            SendVideo::execute($video);
        }
        return response()->json(['data' => true]);
    }
}
