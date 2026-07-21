<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function __construct(private NotificationService $notif) {}

    public function create(array $data, int $userId, int $tenantId): Ticket
    {
        return DB::transaction(function () use ($data, $userId, $tenantId) {
            $ticket = Ticket::create([
                'tenant_id'   => $tenantId,
                'user_id'     => $userId,
                'status'      => Ticket::STATUS_OPEN,
                'priority'    => $data['priority'] ?? 'normal',
                'category'    => $data['category'] ?? 'general',
                'subject'     => $data['subject'],
                'description' => $data['description'],
            ]);

            // اعلان به super admin‌ها
            $this->notif->sendToSuperAdmins(
                type:       'ticket_created',
                title:      'تیکت جدید: ' . $ticket->subject,
                body:       'از ' . $ticket->user->name . ' — اولویت: ' . Ticket::priorityLabels()[$ticket->priority],
                icon:       'message-circle',
                color:      'warning',
                actionUrl:  '/super-admin/tickets/' . $ticket->id,
            );

            return $ticket;
        });
    }

    public function reply(Ticket $ticket, string $body, int $userId, bool $isStaff = false): TicketReply
    {
        return DB::transaction(function () use ($ticket, $body, $userId, $isStaff) {
            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $userId,
                'body'      => $body,
                'is_staff'  => $isStaff,
            ]);

            // تغییر وضعیت
            if ($isStaff) {
                $ticket->update(['status' => Ticket::STATUS_WAITING_USER]);
                // اعلان به کاربر
                $this->notif->send(
                    userId:    $ticket->user_id,
                    type:      'ticket_reply',
                    title:     'پاسخ جدید به تیکت ' . $ticket->ticket_number,
                    body:      mb_substr($body, 0, 100),
                    icon:      'message-circle',
                    color:     'info',
                    actionUrl: '/tickets/' . $ticket->id,
                );
            } else {
                $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
                // اعلان به کارشناس (اگر assigned شده)
                if ($ticket->assigned_to) {
                    $this->notif->send(
                        userId:    $ticket->assigned_to,
                        type:      'ticket_reply',
                        title:     'پاسخ کاربر به تیکت ' . $ticket->ticket_number,
                        body:      mb_substr($body, 0, 100),
                        icon:      'message-circle',
                        color:     'primary',
                        actionUrl: '/super-admin/tickets/' . $ticket->id,
                    );
                }
            }

            return $reply;
        });
    }

    public function changeStatus(Ticket $ticket, string $status): void
    {
        $update = ['status' => $status];
        if ($status === Ticket::STATUS_RESOLVED) $update['resolved_at'] = now();
        if ($status === Ticket::STATUS_CLOSED)   $update['closed_at']   = now();
        $ticket->update($update);

        if (in_array($status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])) {
            $this->notif->send(
                userId:    $ticket->user_id,
                type:      'ticket_closed',
                title:     'تیکت ' . $ticket->ticket_number . ' ' . Ticket::statusLabels()[$status],
                body:      null,
                icon:      'check-circle',
                color:     'success',
                actionUrl: '/tickets/' . $ticket->id,
            );
        }
    }

    public function assign(Ticket $ticket, int $assignedTo): void
    {
        $ticket->update(['assigned_to' => $assignedTo, 'status' => Ticket::STATUS_IN_PROGRESS]);
    }
}
