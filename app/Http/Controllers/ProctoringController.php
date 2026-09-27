<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProctoringLog;

class ProctoringController extends Controller
{
    public function index()
    {
        return view('proctoring-demo');
    }

    public function storeLog(Request $request)
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