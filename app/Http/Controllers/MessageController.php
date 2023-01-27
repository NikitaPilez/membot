<?php

namespace App\Http\Controllers;

use App\Helpers\Telegram\SendMessage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class MessageController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendMessage(Request $request): JsonResponse
    {
        return response()->json(['data' => SendMessage::execute($request->input('message', 'Empty text'))]);
    }
}
