<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->orderBy('is_read')
            ->orderByDesc('created_at')
            ->paginate(12);

        $unreadCount = DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function markAsRead(int $notification)
    {
        DB::table('user_notifications')
            ->where('id', $notification)
            ->where('user_id', Auth::id())
            ->update([
                'is_read'    => true,
                'read_at'    => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        DB::table('user_notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read'    => true,
                'read_at'    => now(),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(int $notification)
    {
        DB::table('user_notifications')
            ->where('id', $notification)
            ->where('user_id', Auth::id())
            ->delete();

        return back()->with('success', 'Notification deleted.');
    }
}