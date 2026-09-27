<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications-index', compact('notifications'));
    }

    public function read(Request $request, string $notification)
    {
        $item = $request->user()
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail();

        $item->markAsRead();

        if (!empty($item->data['url'])) {
            return redirect($item->data['url']);
        }

        return back();
    }

    public function readAll(Request $request)
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('status', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}