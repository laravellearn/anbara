<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\Request;

class SuperTicketController extends Controller
{
    public function __construct(private TicketService $service) {}

    public function index(Request $request)
    {
        $query = Ticket::with(['user', 'assignedUser'])->latest();

        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('priority'))  $query->where('priority', $request->priority);
        if ($request->filled('category'))  $query->where('category', $request->category);
        if ($request->filled('tenant_id')) $query->where('tenant_id', $request->tenant_id);

        $tickets = $query->paginate(25)->withQueryString();
        $stats   = [
            'open'     => Ticket::whereIn('status', [Ticket::STATUS_OPEN, Ticket::STATUS_IN_PROGRESS])->count(),
            'waiting'  => Ticket::where('status', Ticket::STATUS_WAITING_USER)->count(),
            'resolved' => Ticket::where('status', Ticket::STATUS_RESOLVED)->count(),
            'total'    => Ticket::count(),
        ];
        $agents = User::whereNull('tenant_id')->orderBy('name')->get(); // super-admin staff

        return view('super-admin.tickets.index', compact('tickets', 'stats', 'agents'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('replies.user', 'user');
        $agents = User::whereNull('tenant_id')->orderBy('name')->get();
        return view('super-admin.tickets.show', compact('ticket', 'agents'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['body' => 'required|string|max:5000']);
        $this->service->reply($ticket, $request->body, auth()->id(), true);
        return back()->with('success', 'پاسخ ارسال شد.');
    }

    public function changeStatus(Request $request, Ticket $ticket)
    {
        $request->validate(['status' => 'required|in:open,in_progress,waiting_user,resolved,closed']);
        $this->service->changeStatus($ticket, $request->status);
        return back()->with('success', 'وضعیت تیکت تغییر کرد.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $request->validate(['assigned_to' => 'required|exists:users,id']);
        $this->service->assign($ticket, $request->assigned_to);
        return back()->with('success', 'تیکت تخصیص داده شد.');
    }
}
