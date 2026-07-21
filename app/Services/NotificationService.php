<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;

class NotificationService
{
    /**
     * ارسال اعلان به یک کاربر مشخص
     */
    public function send(
        int $tenantId,
        int $userId,
        string $type,
        string $title,
        string $body = '',
        string $actionUrl = '',
        $notifiable = null
    ): AppNotification {
        $config = AppNotification::typeConfig()[$type] ?? ['icon' => 'bell', 'color' => 'primary'];

        return AppNotification::create([
            'tenant_id'       => $tenantId,
            'user_id'         => $userId,
            'type'            => $type,
            'title'           => $title,
            'body'            => $body,
            'icon'            => $config['icon'],
            'color'           => $config['color'],
            'action_url'      => $actionUrl,
            'is_read'         => false,
            'notifiable_type' => $notifiable ? get_class($notifiable) : null,
            'notifiable_id'   => $notifiable?->id,
        ]);
    }

    /**
     * ارسال اعلان به همه مدیران یک tenant
     */
    public function sendToTenantAdmins(
        int $tenantId,
        string $type,
        string $title,
        string $body = '',
        string $actionUrl = '',
        $notifiable = null
    ): void {
        $admins = User::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->where(fn($q) => $q->where('is_tenant_admin', true)->orWhereHas('companyUsers.roles', fn($r) => $r->where('is_admin', true)))
            ->pluck('id');

        foreach ($admins as $uid) {
            $this->send($tenantId, $uid, $type, $title, $body, $actionUrl, $notifiable);
        }
    }

    /**
     * علامت‌گذاری اعلان‌ها به عنوان خوانده شده
     */
    public function markRead(int $userId, ?int $notificationId = null): int
    {
        $query = AppNotification::forUser($userId)->unread();

        if ($notificationId) {
            $query->where('id', $notificationId);
        }

        return $query->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * تعداد اعلان‌های خوانده‌نشده یک کاربر
     */
    public function unreadCount(int $userId): int
    {
        return AppNotification::forUser($userId)->unread()->count();
    }

    /**
     * آخرین N اعلان یک کاربر
     */
    public function latest(int $userId, int $limit = 10)
    {
        return AppNotification::forUser($userId)->latest()->limit($limit)->get();
    }

    /**
     * حذف اعلان‌های قدیمی (بیش از ۶۰ روز)
     */
    public function cleanup(int $daysOld = 60): int
    {
        return AppNotification::where('created_at', '<', now()->subDays($daysOld))
            ->where('is_read', true)
            ->delete();
    }
}
