<?php

namespace App\Helpers\Download;

use App\DTO\GetContentUrlDTO;
use Exception;
use Illuminate\Support\Facades\Log;

class TikTokContentVideo implements ContentVideoInterface
{

    public function getContent(string $sourceUrl)
    {
        $ch = curl_init();

        $options = [
            CURLOPT_URL            => $sourceUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_HTTPHEADER     => [
                'Range: bytes=0-'
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLINFO_HEADER_OUT    => true,
            CURLOPT_USERAGENT      => 'okhttp',
            CURLOPT_ENCODING       => "utf-8",
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_COOKIEJAR      => 'cookie.txt',
            CURLOPT_COOKIEFILE     => 'cookie.txt',
            CURLOPT_REFERER        => 'https://www.tiktok.com/',
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_MAXREDIRS      => 10,
        ];

        curl_setopt_array($ch, $options);

        $data = curl_exec($ch);

        curl_close($ch);

        return $data;
    }

    public function getContentUrl(string $videoUrl): GetContentUrlDTO
    {
        try {
            $ch = curl_init();

            $options = [
                CURLOPT_URL            => $videoUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.111 Mobile Safari/537.36',
                CURLOPT_ENCODING       => "utf-8",
                CURLOPT_AUTOREFERER    => false,
                CURLOPT_COOKIEJAR      => 'cookie.txt',
                CURLOPT_COOKIEFILE     => 'cookie.txt',
                CURLOPT_REFERER        => 'https://www.tiktok.com/',
                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_MAXREDIRS      => 10,
            ];

            curl_setopt_array($ch, $options);

            $data = curl_exec($ch);

            curl_close($ch);

            $content = strval($data);

            $encodedUrlArr = explode('"downloadAddr":"', $content);

            $encodedUrl = explode("\"", $encodedUrlArr[1])[0];
            $sourceUrl = $this->escape_sequence_decode($encodedUrl);
        } catch (Exception $exception) {
            return new GetContentUrlDTO(
                success: false,
                message: $exception->getMessage(),
            );
        }

        return new GetContentUrlDTO(
            success: true,
            sourceUrl: $sourceUrl,
        );
    }

    private function escape_sequence_decode(string $str)
    {
        $regex = '/\\\u([dD][89abAB][\da-fA-F]{2})\\\u([dD][c-fC-F][\da-fA-F]{2})|\\\u([\da-fA-F]{4})/sx';

        return preg_replace_callback($regex, function ($matches) {
            if (isset($matches[3])) {
                $cp = hexdec($matches[3]);
            } else {
                $lead = hexdec($matches[1]);
                $trail = hexdec($matches[2]);

                $cp = ($lead << 10) + $trail + 0x10000 - (0xD800 << 10) - 0xDC00;
            }

            if ($cp > 0xD7FF && 0xE000 > $cp) {
                $cp = 0xFFFD;
            }

            if ($cp < 0x80) {
                return chr($cp);
            } else if ($cp < 0xA0) {
                return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
            }

            return html_entity_decode('&#' . $cp . ';');
        }, $str);
    }
}
