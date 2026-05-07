<?php

namespace App\Enums;

enum NotificationType: string
{
    case AnnouncementAcademic = 'announcement_academic';   // pengumuman akademis
    case AnnouncementGeneral = 'announcement_general';   // pengumuman umum
    case LostAndFound = 'lost_and_found'; // kehilangan dan penemuan
    case EmergencyInfo = 'emergency_info'; // info darurat
    case ClassCancelled = 'class_cancelled'; // kelas dibatalkan
    case AnnouncementForClass = 'announcement_for_class'; // pengumuman untuk kelas tertentu
    case Assignment = 'assignment'; // tugas atau pekerjaan rumah
    case Permission = 'permission'; // izin (misal izin tidak masuk) (dari siswa ke sistem).
    case PermissionAccepted = 'permission_accepted'; // izin disetujui
    case PermissionRejected = 'permission_rejected'; // izin ditolak
    case AttendanceViolation = 'attendance_violation'; // pelanggaran kehadiran (misal terlambat, bolos, dll)
    case PersonalNote = 'personal_note'; // catatan pribadi (misal guru memberikan catatan khusus untuk siswa tertentu)
}
