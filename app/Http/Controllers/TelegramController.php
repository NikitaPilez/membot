<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Video;
use App\Services\SendVideoInTelegramService;
use Illuminate\Http\RedirectResponse;

class TelegramController extends Controller
{
    private SendVideoInTelegramService $sendVideoInTelegramService;

    public function __construct(SendVideoInTelegramService $sendVideoInTelegramService)
    {
        $this->sendVideoInTelegramService = $sendVideoInTelegramService;
    }

    public function send(?Video $video): RedirectResponse
    {
        $this->sendVideoInTelegramService->sendVideoInTelegram($video);

        return back();
    }
}
