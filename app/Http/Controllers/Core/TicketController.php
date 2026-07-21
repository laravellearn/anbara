<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function __construct(private TicketService $service) {}

    public function index(Request $request)
    {
        Gate::authorize('access', 'tickets.view');
        $tenantId = auth()->user()->tenant_id;

        $query = Ticket::with('user')
            ->where('tenant_id', $tenantId)
            ->latest();

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('category')) $query->where('category', $request->category);

        $tickets = $query->paginate(20)->withQueryString();
        $stats   = [
            'open'     => Ticket::where('tenant_id', $tenantId)->where('status', Ticket::STATUS_OPEN)->count(),
            'progress' => Ticket::where('tenant_id', $tenantId)->where('status', Ticket::STATUS_IN_PROGRESS)->count(),
            'resolved' => Ticket::where('tenant_id', $tenantId)->where('status', Ticket::STATUS_RESOLVED)->count(),
            'total'    => Ticket::where('tenant_id', $tenantId)->count(),
        ];

        return view('core.tickets.index', compact('tickets', 'stats'));
    }

    public function create() {
        Gate::authorize('access', 'tickets.create');
        return view('core.tickets.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'tickets.create');
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'category'    => 'required|in:general,billing,technical,warehouse',
            'priority'    => 'required|in:low,normal,high,urgent',
            'description' => 'required|string|max:5000',
        ]);

        $ticket = $this->service->create($data, auth()->id(), auth()->user()->tenant_id);
        return redirect()->route('tickets.show', $ticket)->with('success', 'تیکت با شماره ' . $ticket->ticket_number . ' ثبت شد.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('access', 'tickets.view');
        abort_unless($ticket->tenant_id === auth()->user()->tenant_id, 403);
        $ticket->load('replies.user');
        return view('core.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        Gate::authorize('access', 'tickets.reply');
        abort_unless($ticket->tenant_id === auth()->user()->tenant_id, 403);
        abort_unless($ticket->canReply(), 422, 'تیکت بسته است.');
        $request->validate(['body' => 'required|string|max:5000']);
        $this->service->reply($ticket, $request->body, auth()->id(), false);
        return back()->with('success', 'پاسخ ارسال شد.');
    }
}
