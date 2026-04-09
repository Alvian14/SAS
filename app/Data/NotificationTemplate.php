<?php

namespace App\Data;

class NotificationTemplate
{
    public const TARGET_TOKEN = 'token';
    public const TARGET_TOPIC = 'topic';

    public function __construct(
        public readonly string $targetType = self::TARGET_TOKEN,
        public readonly ?string $targetValue = null,
        public readonly string $title = 'Judul Notifikasi',
        public readonly string $body = 'Isi notifikasi',
        public readonly array $data = [],
    ) {
    }

    public static function excelAliases(): array
    {
        return [
            'target_type' => ['target_type', 'target', 'send_type'],
            'target_value' => ['target_value', 'target_to', 'send_to'],
            'token' => ['token', 'fcm_token'],
            'topic' => ['topic', 'fcm_topic'],
            'title' => ['title', 'judul'],
            'body' => ['body', 'isi'],
            'data' => ['data', 'payload'],
        ];
    }

    public static function excelColumns(): array
    {
        return ['target_type', 'target_value', 'title', 'body', 'data'];
    }

    public static function example(): array
    {
        return [
            'target_type' => self::TARGET_TOKEN,
            'target_value' => 'fcm_token_device',
            'title' => 'Judul Notifikasi',
            'body' => 'Isi notifikasi',
            'data' => [
                'type' => 'announcement',
                'source' => 'excel-import',
            ],
        ];
    }

    public static function topicExample(): array
    {
        return [
            'target_type' => self::TARGET_TOPIC,
            'target_value' => 'student_notifications',
            'title' => 'Pengumuman Kelas',
            'body' => 'Ada update jadwal hari ini',
            'data' => [
                'type' => 'class_announcement',
                'source' => 'excel-import',
            ],
        ];
    }

    public static function forToken(string $token, string $title, string $body, array $data = []): self
    {
        return new self(
            targetType: self::TARGET_TOKEN,
            targetValue: $token,
            title: $title,
            body: $body,
            data: $data,
        );
    }

    public static function forTopic(string $topic, string $title, string $body, array $data = []): self
    {
        return new self(
            targetType: self::TARGET_TOPIC,
            targetValue: $topic,
            title: $title,
            body: $body,
            data: $data,
        );
    }

    public static function fromArray(array $attributes): self
    {
        $targetType = self::normalizeTargetType(
            self::pickValue($attributes, self::excelAliases()['target_type'])
        );

        $targetValue = self::pickValue($attributes, self::excelAliases()['target_value']);

        if (!$targetValue) {
            $targetValue = $targetType === self::TARGET_TOPIC
                ? self::pickValue($attributes, self::excelAliases()['topic'])
                : self::pickValue($attributes, self::excelAliases()['token']);
        }

        if (!$targetValue && self::pickValue($attributes, self::excelAliases()['topic'])) {
            $targetType = self::TARGET_TOPIC;
            $targetValue = self::pickValue($attributes, self::excelAliases()['topic']);
        }

        return new self(
            targetType: $targetType,
            targetValue: $targetValue,
            title: self::pickValue($attributes, self::excelAliases()['title']) ?? 'Judul Notifikasi',
            body: self::pickValue($attributes, self::excelAliases()['body']) ?? 'Isi notifikasi',
            data: self::normalizeData(self::pickValue($attributes, self::excelAliases()['data'])),
        );
    }

    public function toArray(): array
    {
        return [
            'target_type' => $this->targetType,
            'target_value' => $this->targetValue,
            'token' => $this->isToken() ? $this->targetValue : null,
            'topic' => $this->isTopic() ? $this->targetValue : null,
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }

    public function isToken(): bool
    {
        return $this->targetType === self::TARGET_TOKEN;
    }

    public function isTopic(): bool
    {
        return $this->targetType === self::TARGET_TOPIC;
    }

    public function target(): ?string
    {
        return $this->targetValue;
    }

    private static function pickValue(array $attributes, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $attributes)) {
                return $attributes[$key];
            }
        }

        return null;
    }

    private static function normalizeData(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : ['raw' => $value];
        }

        return [];
    }

    private static function normalizeTargetType(mixed $value): string
    {
        if (!is_string($value) || $value === '') {
            return self::TARGET_TOKEN;
        }

        $normalized = strtolower(trim($value));

        return match ($normalized) {
            'topic', 'fcm_topic' => self::TARGET_TOPIC,
            default => self::TARGET_TOKEN,
        };
    }
}