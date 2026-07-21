<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $service) {}

    /** لیست اعلان‌ها (صفحه کامل) */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $query  = AppNotification::forUser($userId)->latest();

        if ($request->filled('unread')) {
            $query->unread();
        }

        $notifications = $query->paginate(20);
        $unreadCount   = $this->service->unreadCount($userId);

        return view('core.notifications.index', compact('notifications', 'unreadCount'));
    }

    /** AJAX: آخرین اعلان‌ها برای dropdown */
    public function latest()
    {
        $userId        = auth()->id();
        $notifications = $this->service->latest($userId, 10);
        $unreadCount   = $this->service->unreadCount($userId);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    /** علامت‌گذاری یک یا همه اعلان‌ها به عنوان خوانده‌شده */
    public function markRead(Request $request, ?int $id = null)
    {
        $count = $this->service->markRead(auth()->id(), $id);

        if ($request->expectsJson()) {
            return response()->json(['marked' => $count, 'unread_count' => $this->service->unreadCount(auth()->id())]);
        }

        return redirect()->back()->with('toast', ['message' => 'اعلان‌ها خوانده شدند.', 'type' => 'success', 'title' => 'اعلان']);
    }

    /** حذف یک اعلان */
    public function destroy(AppNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        $notification->delete();

        return response()->json(['ok' => true]);
    }
}
