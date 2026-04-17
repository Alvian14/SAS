<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
    // table name
    protected $table = 'notifications';

    protected $fillable = ["title", "body", "type", "send_to", "sender_id", "receiver_id", "class_id"];

    public $timestamps = true;

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    protected static function booted()
    {
        static::created(function ($notif) {
            try {
                $adminPhone = '62' . ltrim(env('FONNTE_PHONE'), '0');

                Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->post('https://api.fonnte.com/send', [
                    'phone' => $adminPhone,
                    'message' =>
                        "🔔 NOTIFIKASI BARU\n\n" .
                        "Judul: {$notif->title}\n" .
                        "Pesan: {$notif->body}",
                ]);

            } catch (\Exception $e) {
                Log::error('WA Error: ' . $e->getMessage());
            }
        });
    }
}
