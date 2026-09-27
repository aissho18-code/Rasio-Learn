<?php

namespace App\Support;

use App\Models\Kelas;
use App\Models\User;
use App\Notifications\LearningNotification;

class LearningNotificationService
{
    public function notifyStudentsInClass(int $kelasId, LearningNotification $notification): void
    {
        User::forRoles('siswa')
            ->whereHas('siswaProfile', fn ($query) => $query->where('kelas_id', $kelasId))
            ->each(fn (User $student) => $this->sendOnce($student, $notification));
    }

    public function notifyAllStudents(LearningNotification $notification): void
    {
        User::forRoles('siswa')->each(fn (User $student) => $this->sendOnce($student, $notification));
    }

    public function notifyClassTeacher(int $kelasId, LearningNotification $notification): void
    {
        $teacherId = Kelas::whereKey($kelasId)->value('wali_kelas_id');
        $teacher = $teacherId ? User::forRoles('guru')->find($teacherId) : null;

        if ($teacher) {
            $this->sendOnce($teacher, $notification);
        }
    }

    public function notifyOtherAdmins(int $actorId, LearningNotification $notification): void
    {
        User::forRoles('admin')
            ->whereKeyNot($actorId)
            ->each(fn (User $admin) => $this->sendOnce($admin, $notification));
    }

    public function notifyAdmins(LearningNotification $notification): void
    {
        User::forRoles('admin')->each(fn (User $admin) => $this->sendOnce($admin, $notification));
    }

    public function sendOnce(User $recipient, LearningNotification $notification): void
    {
        if ($notification->eventKey !== null && $recipient->notifications()
            ->where('data->event_key', $notification->eventKey)
            ->exists()) {
            return;
        }

        $recipient->notify($notification);
    }
}