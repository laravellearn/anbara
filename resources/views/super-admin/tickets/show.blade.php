@extends('super-admin.layouts.master')
@section('title','تیکت '.$ticket->ticket_number)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @php $sc=\App\Models\Ticket::statusColors();$sl=\App\Models\Ticket::statusLabels(); @endphp
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">اطلاعات تیکت</h6></div>
        <div class="card-body">
          <table class="table table-sm">
            <tr><td class="text-muted">شماره</td><td><strong>{{ $ticket->ticket_number }}</strong></td></tr>
            <tr><td class="text-muted">سازمان</td><td>{{ $ticket->tenant?->name ?? '—' }}</td></tr>
            <tr><td class="text-muted">کاربر</td><td>{{ $ticket->user->name }}</td></tr>
            <tr><td class="text-muted">وضعیت</td><td><span class="badge bg-label-{{ $sc[$ticket->status]??'secondary' }}">{{ $sl[$ticket->status]??$ticket->status }}</span></td></tr>
          </table>
        </div>
      </div>

      {{-- تغییر وضعیت --}}
      <div class="card mb-3">
        <div class="card-header"><h6 class="mb-0">تغییر وضعیت</h6></div>
        <div class="card-body">
          <form action="{{ route('super-admin.tickets.status',$ticket) }}" method="POST" class="d-flex gap-2">
            @csrf
            <select name="status" class="form-select form-select-sm">
              @foreach($sl as $k=>$v)
                <option value="{{ $k }}" {{ $ticket->status===$k?'selected':'' }}>{{ $v }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-primary">ثبت</button>
          </form>
        </div>
      </div>

      {{-- تخصیص --}}
      <div class="card">
        <div class="card-header"><h6 class="mb-0">تخصیص به کارشناس</h6></div>
        <div class="card-body">
          <form action="{{ route('super-admin.tickets.assign',$ticket) }}" method="POST" class="d-flex gap-2">
            @csrf
            <select name="assigned_to" class="form-select form-select-sm">
              <option value="">انتخاب کنید</option>
              @foreach($agents as $a)
                <option value="{{ $a->id }}" {{ $ticket->assigned_to==$a->id?'selected':'' }}>{{ $a->name }}</option>
              @endforeach
            </select>
            <button class="btn btn-sm btn-outline-primary">تخصیص</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card mb-3 border-start border-4 border-primary">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <strong>{{ $ticket->user->name }}</strong>
            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
          </div>
          <p class="fw-semibold mb-1">{{ $ticket->subject }}</p>
          <p class="mb-0">{{ $ticket->description }}</p>
        </div>
      </div>

      @foreach($ticket->replies as $reply)
      <div class="card mb-2 border-start border-4 border-{{ $reply->is_staff?'success':'info' }}">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span><strong>{{ $reply->user->name }}</strong>
              @if($reply->is_staff)<span class="badge bg-label-success ms-1">کارشناس</span>@endif
            </span>
            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
          </div>
          <p class="mb-0">{{ $reply->body }}</p>
        </div>
      </div>
      @endforeach

      @if($ticket->canReply())
      <div class="card mt-3">
        <div class="card-body">
          <form action="{{ route('super-admin.tickets.reply',$ticket) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">پاسخ کارشناس</label>
              <textarea name="body" rows="4" class="form-control" required></textarea>
            </div>
            <div class="text-end">
              <button class="btn btn-success">ارسال پاسخ</button>
            </div>
          </form>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
