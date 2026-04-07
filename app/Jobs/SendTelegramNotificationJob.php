<?php

namespace App\Jobs;

use App\Services\TelegramNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendTelegramNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public array $payload)
    {
    }

    public function handle(TelegramNotifier $telegramNotifier): void
    {
        $title = (string) ($this->payload['title'] ?? 'Notifikasi Baru');
        $body = (string) ($this->payload['body'] ?? '');
        $sender = (string) ($this->payload['sender'] ?? 'Sistem');
        $className = (string) ($this->payload['class'] ?? '-');
        $time = (string) ($this->payload['time'] ?? now()->format('d M Y H:i'));

        $message = "<b>Notifikasi Baru</b>\n"
            . "<b>Judul:</b> {$title}\n"
            . "<b>Pesan:</b> {$body}\n"
            . "<b>Pengirim:</b> {$sender}\n"
            . "<b>Kelas:</b> {$className}\n"
            . "<b>Waktu:</b> {$time}";

        $telegramNotifier->sendMessage($message);
    }
}
