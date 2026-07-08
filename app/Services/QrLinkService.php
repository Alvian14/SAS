<?php

namespace App\Services;

use App\Models\Schedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class QrLinkService
{
    /**
     * Generate a public QR link for a schedule
     * 
     * @param int $scheduleId
     * @return array
     */
    public function generateLink($scheduleId)
    {
        // Verify schedule exists
        $schedule = Schedule::findOrFail($scheduleId);

        // Generate random token
        $token = Str::random(64);

        // Create expiration time (10 minutes from now)
        $expiresAt = now()->addSeconds(600);

        // Cache the schedule ID and expiration time
        Cache::put("public_qr_link:$token", [
            'schedule_id' => $scheduleId,
            'expires_at' => $expiresAt->toIso8601String()
        ], $expiresAt);

        // Generate the public URL
        $url = route('jadwal.qr.public', ['token' => $token]);

        return [
            'success' => true,
            'message' => 'QR link generated',
            'data' => [
                'url' => $url,
                'token' => $token,
                'schedule_id' => $scheduleId,
                'expired_in_seconds' => 600,
                'expired_in_minutes' => 10
            ]
        ];
    }

    /**
     * Get the schedule ID and expiration from a token
     * 
     * @param string $token
     * @return array|null
     */
    public function getTokenData($token)
    {
        return Cache::get("public_qr_link:$token");
    }

    /**
     * Invalidate a token
     * 
     * @param string $token
     * @return void
     */
    public function invalidateToken($token)
    {
        Cache::forget("public_qr_link:$token");
    }
}
