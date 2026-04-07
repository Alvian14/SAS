<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    public function sendMessage(string $message): bool
    {
        $token = trim((string) config('services.telegram.bot_token'));
        $chatId = trim((string) config('services.telegram.chat_id'));

        if ($token === '' || $chatId === '') {
            Log::warning('Telegram notification skipped: missing bot token or chat id.');
            return false;
        }

        $response = Http::timeout(10)
            ->acceptJson()
            ->asForm()
            ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ]);

        if (! $response->successful()) {
            Log::error('Telegram send failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }
}
