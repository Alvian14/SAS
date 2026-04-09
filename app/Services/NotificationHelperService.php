<?php

namespace App\Services;

use App\Data\NotificationCreateTemplate;
use App\Data\NotificationTemplate;
use App\Enums\NotificationType;
use Illuminate\Http\UploadedFile;

class NotificationHelperService
{
    public function __construct(protected FirebaseMessagingService $fcm)
    {
    }

    public function template(array $attributes = []): NotificationTemplate
    {
        return NotificationTemplate::fromArray($attributes);
    }

    public function createTemplateForAll(
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): NotificationCreateTemplate {
        return NotificationCreateTemplate::forAll($title, $body, $type, $senderId);
    }

    public function createTemplateForClass(
        int $classId,
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): NotificationCreateTemplate {
        return NotificationCreateTemplate::forClass($classId, $title, $body, $type, $senderId);
    }

    public function createTemplateForStudent(
        int $receiverId, // user id siswa
        string $title,
        string $body,
        NotificationType|string $type,
        ?int $senderId = null,
    ): NotificationCreateTemplate {
        return NotificationCreateTemplate::forStudent($receiverId, $title, $body, $type, $senderId);
    }

    public function tokenTemplate(string $token, string $title, string $body, array $data = []): NotificationTemplate
    {
        return NotificationTemplate::forToken($token, $title, $body, $data);
    }

    public function topicTemplate(string $topic, string $title, string $body, array $data = []): NotificationTemplate
    {
        return NotificationTemplate::forTopic($topic, $title, $body, $data);
    }



    /// template messaging general.
    public function templateAnnouncementForAll(string $judul, string $isi, string $topic = 'student_notifications', array $data = []): NotificationTemplate
    {
        return $this->topicTemplate(
            $topic,
            $judul,
            $isi,
            array_merge([
                'type' => 'announcement',
                'level' => 'all',
            ], $data),
        );
    }

    public function templateAnnouncementForClass(string $classTopic, string $judul, string $isi, array $data = []): NotificationTemplate
    {
        return $this->topicTemplate(
            $classTopic,
            $judul,
            $isi,
            array_merge([
                'type' => 'announcement',
                'level' => 'class',
            ], $data),
        );
    }

    public function templateAssignmentForClass(string $classTopic, string $judul = null, string $isi, array $data = []): NotificationTemplate
    {
        return $this->topicTemplate(
            $classTopic,
            $judul ?? 'Tugas Baru',
            $isi,
            array_merge([
                'type' => 'assignment',
                'level' => 'class',
            ], $data),
        );
    }

    public function templateTokenPersonal(string $token, string $judul, string $isi, array $data = []): NotificationTemplate
    {
        return $this->tokenTemplate(
            $token,
            $judul,
            $isi,
            array_merge([
                'level' => 'student',
            ], $data),
        );
    }


    /// template messaging based on rules template data.
    public function templateAnnouncementAcademic($message): NotificationTemplate{
        return $this->topicTemplate(
            'student_notifications',
            'Pengumuman Akademik',
            $message,
            [
                'type' => 'announcement_academic',
                'level' => 'all',
            ],
        );
    }

    public function templateAnnouncementGeneral($message): NotificationTemplate{
        return $this->topicTemplate(
            'student_notifications',
            'Pengumuman Umum',
            $message,
            [
                'type' => 'announcement_general',
                'level' => 'all',
            ],
        );
    }

    public function templateLostAndFound($message): NotificationTemplate{
        return $this->topicTemplate(
            'student_notifications',
            'Info Kehilangan dan Penemuan',
            $message,
            [
                'type' => 'lost_and_found',
                'level' => 'all',
            ],
        );
    }

    public function templateEmergencyInfo($message): NotificationTemplate{
        return $this->topicTemplate(
            'student_notifications',
            'Info Darurat',
            $message,
            [
                'type' => 'emergency_info',
                'level' => 'all',
            ],
        );
    }

    public function templateClassCancelled($message): NotificationTemplate{
        return $this->topicTemplate(
            'student_notifications',
            'Kelas Dibatalkan',
            $message,
            [
                'type' => 'class_cancelled',
                'level' => 'all',
            ],
        );
    }

    public function templatePermissionRequest($studentTopic, $message): NotificationTemplate{
        return $this->tokenTemplate(
            $studentTopic,
            'Permohonan Izin',
            $message,
            [
                'type' => 'permission',
                'level' => 'student',
            ],
        );
    }

    public function templatePermissionApproval($studentTopic, $message = null): NotificationTemplate{
        return $this->tokenTemplate(
            $studentTopic,
            'Perizinan Disetujui',
            $message ?? 'Permohonan izin Anda telah disetujui.',
            [
                'type' => 'permission_approval',
                'level' => 'student',
            ],
        );
    }

    public function templatePermissionRejection($studentTopic, $message = null): NotificationTemplate{
        return $this->tokenTemplate(
            $studentTopic,
            'Perizinan Ditolak',
            $message ?? 'Permohonan izin Anda telah ditolak.',
        [
            'type' => 'permission_rejection',
            'level' => 'student',
        ],);
    }

    public function templateAttendanceViolation($studentTopic, $message = null): NotificationTemplate{
        return $this->tokenTemplate(
            $studentTopic,
            'Anda Melanggar Aturan Absensi',
            $message ?? 'Anda telah melanggar aturan absensi.',
            [
                'type' => 'attendance_violation',
                'level' => 'student',
            ],
        );
    }

    public function templatePersonalNote($studentTopic, $message): NotificationTemplate{
        return $this->tokenTemplate(
            $studentTopic,
            'Catatan Pribadi',
            $message,
            [
                'type' => 'personal_note',
                'level' => 'student',
            ],
        );
    }

    public function excelTemplate(): array
    {
        return [
            'columns' => NotificationTemplate::excelColumns(),
            'aliases' => NotificationTemplate::excelAliases(),
            'example' => NotificationTemplate::example(),
            'topic_example' => NotificationTemplate::topicExample(),
        ];
    }

    public function send(NotificationTemplate $template): mixed
    {
        if (!$template->target()) {
            throw new \InvalidArgumentException('Notification target is required.');
        }

        if ($template->isTopic()) {
            return $this->fcm->sendToTopic(
                $template->target(),
                $template->title,
                $template->body,
                $template->data,
            );
        }

        return $this->fcm->sendToToken(
            $template->target(),
            $template->title,
            $template->body,
            $template->data,
        );
    }

    /**
     * Parse an uploaded Excel file (or path) and return an array of NotificationTemplate objects.
     *
     * Implementation TODO: use PhpSpreadsheet or similar to read the Excel file
     * and map columns to NotificationTemplate using the aliases defined there.
     *
     * @param UploadedFile|string $file
     * @return array<int, NotificationTemplate>
     * @throws \RuntimeException when not implemented
     */
    public function parseExcelNotifications(UploadedFile|string $file): array
    {
        // TODO: implement parsing logic
        throw new \RuntimeException('NotificationHelperService::parseExcelNotifications not implemented');
    }

    /**
     * Build a normalized notification template from a row array.
     *
     * @param array $row
     * @return NotificationTemplate
     */
    public function buildPayload(array $row): NotificationTemplate
    {
        return $this->template($row);
    }
}
