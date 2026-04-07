<?php

namespace App\Providers;

use App\Jobs\SendTelegramNotificationJob;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, Client::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
        Carbon::setLocale('id');

        Model::created(function (Model $model): void {
            $tableName = strtolower((string) $model->getTable());
            $modelBaseName = class_basename($model);
            $attributes = $model->getAttributes();

            $isNotificationModel = in_array($tableName, ['notifications', 'notifikasi', 'notifikasis'], true)
                || in_array($modelBaseName, ['Notification', 'Notifikasi'], true)
                || (array_key_exists('title', $attributes) && array_key_exists('body', $attributes));

            if (! $isNotificationModel) {
                return;
            }

            $senderName = optional($model->sender)->name ?? 'Sistem';
            $className = optional($model->class)->name ?? '-';

            // Jalankan sinkron agar tetap terkirim tanpa queue worker (cocok untuk local/XAMPP).
            SendTelegramNotificationJob::dispatchSync([
                'title' => $attributes['title'] ?? 'Notifikasi Baru',
                'body' => $attributes['body'] ?? '',
                'sender' => $senderName,
                'class' => $className,
                'time' => optional($model->created_at)->format('d M Y H:i') ?? now()->format('d M Y H:i'),
            ]);
        });
    }
}
