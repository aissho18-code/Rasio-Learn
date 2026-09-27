<?php

namespace App\Console\Commands;

use App\Models\Submission;
use App\Models\Tugas;
use App\Models\User;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;
use Illuminate\Console\Command;

class SendTaskDeadlineReminders extends Command
{
    protected $signature = 'notifications:task-deadlines';

    protected $description = 'Notify students about tasks due within the next 24 hours.';

    public function handle(LearningNotificationService $notifications): int
    {
        $now = now();
        $tasks = Tugas::query()
            ->where('status', 'aktif')
            ->whereNotNull('tenggat_waktu')
            ->whereBetween('tenggat_waktu', [$now, $now->copy()->addDay()])
            ->with('materi')
            ->get();

        foreach ($tasks as $task) {
            $kelasId = $task->materi?->kelas_id;
            if (!$kelasId) {
                continue;
            }

            $submittedStudentIds = Submission::where('tugas_id', $task->id)->pluck('siswa_id');
            $students = User::forRoles('siswa')
                ->whereHas('siswaProfile', fn ($query) => $query->where('kelas_id', $kelasId))
                ->whereNotIn('id', $submittedStudentIds)
                ->get();

            foreach ($students as $student) {
                $notifications->sendOnce($student, new LearningNotification(
                    type: 'deadline',
                    title: 'Deadline Tugas Mendekat',
                    message: 'Tugas ' . $task->judul . ' harus dikumpulkan sebelum ' . $task->tenggat_waktu->format('d/m H:i') . '.',
                    url: route('siswa.tugas.show', $task->id),
                    eventKey: 'task.deadline:' . $task->id . ':' . $task->tenggat_waktu->timestamp
                ));
            }
        }

        $this->info('Task deadline reminders processed.');

        return self::SUCCESS;
    }
}