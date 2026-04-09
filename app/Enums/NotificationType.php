<?php

namespace App\Enums;

enum NotificationType: string
{
    case AnnouncementAcademic = 'announcement_academic';
    case AnnouncementGeneral = 'announcement_general';
    case LostAndFound = 'lost_and_found';
    case EmergencyInfo = 'emergency_info';
    case ClassCancelled = 'class_cancelled';
    case AnnouncementForClass = 'announcement_for_class';
    case Assignment = 'assignment';
    case Permission = 'permission';
    case AttendanceViolation = 'attendance_violation';
    case PersonalNote = 'personal_note';
}