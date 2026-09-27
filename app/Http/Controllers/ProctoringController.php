<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProctoringLog;
use App\Notifications\LearningNotification;
use App\Support\LearningNotificationService;

class ProctoringController extends Controller
{
    public function index()
    {
        return view('proctoring-demo');
    }

    public function storeLog(Request $request, LearningNotificationService $notifications)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'detail' => 'required|string',
            'severity' => 'required|in:info,warn,critical',
            'examId' => 'nullable|string',
        ]);

        $log = ProctoringLog::create([
            'user_id' => auth()->id(),
            'exam_id' => $validated['examId'] ?? null,
            'type' => $validated['type'],
            'detail' => $validated['detail'],
            'severity' => $validated['severity'],
        ]);

        if (in_array($log->severity, ['warn', 'critical'], true)) {
            $log->load(['user', 'exam']);
            $notifications->notifyAdmins(new LearningNotification(
                type: 'system',
                title: $log->severity === 'critical' ? 'Peringatan Proctoring Kritis' : 'Peringatan Proctoring',
                message: ($log->user?->name ?? 'Siswa') . ' memicu peringatan pada ujian ' . ($log->exam?->title ?? 'tanpa nama') . '.',
                url: route('admin.proctoring'),
                eventKey: 'proctoring.log:' . $log->id
            ));
        }

        return response()->json([
            'success' => true,
            'log' => $log
        ]);
    }

    public function getLogs()
    {
        $logs = ProctoringLog::with('user')->latest()->get();
        return response()->json($logs);
    }
}