<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }
}
