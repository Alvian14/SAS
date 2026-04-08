<?php

namespace App\Data;

use App\Enums\NotificationType;

class NotificationCreateTemplate
{
    public const LEVEL_ALL = 'all';
    public const LEVEL_CLASS = 'class';
    public const LEVEL_STUDENT = 'student';

    public function __construct(
        public readonly string $title,
        public readonly string $body,
        public readonly NotificationType $type,
        public readonly string $level,
        public readonly ?int $senderId = null,
        public readonly ?int $receiverId = null,
        public readonly ?int $classId = null,
    ) {
    }

    public static function forAll(
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): self {
        return new self(
            title: $title,
            body: $body,
            type: self::normalizeType($type),
            level: self::LEVEL_ALL,
            senderId: $senderId,
        );
    }

    public static function forClass(
        int $classId,
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): self {
        return new self(
            title: $title,
            body: $body,
            type: self::normalizeType($type),
            level: self::LEVEL_CLASS,
            senderId: $senderId,
            classId: $classId,
        );
    }

    public static function forStudent(
        int $receiverId,
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): self {
        return new self(
            title: $title,
            body: $body,
            type: self::normalizeType($type),
            level: self::LEVEL_STUDENT,
            senderId: $senderId,
            receiverId: $receiverId,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type->value,
            'send_to' => $this->level,
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'class_id' => $this->classId,
        ];
    }

    private static function normalizeType(NotificationType|string $type): NotificationType
    {
        if ($type instanceof NotificationType) {
            return $type;
        }

        return NotificationType::from($type);
    }
}