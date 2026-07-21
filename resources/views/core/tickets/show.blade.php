@extends('layouts.app')
@section('title', 'تیکت ' . $ticket->ticket_number)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  @php
    $sc = \App\Models\Ticket::statusColors(); $sl = \App\Models\Ticket::statusLabels();
    $pc = \App\Models\Ticket::priorityColors(); $pl = \App\Models\Ticket::priorityLabels();
  @endphp
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card mb-3">
        <div class="card-body">
          <h6 class="fw-bold">{{ $ticket->subject }}</h6>
          <table class="table table-sm mt-2">
            <tr><td class="text-muted">شماره</td><td><strong>{{ $ticket->ticket_number }}</strong></td></tr>
            <tr><td class="text-muted">وضعیت</td><td><span class="badge bg-label-{{ $sc[$ticket->status]??'secondary' }}">{{ $sl[$ticket->status]??$ticket->status }}</span></td></tr>
            <tr><td class="text-muted">اولویت</td><td><span class="badge bg-label-{{ $pc[$ticket->priority]??'secondary' }}">{{ $pl[$ticket->priority]??$ticket->priority }}</span></td></tr>
            <tr><td class="text-muted">دسته</td><td>{{ \App\Models\Ticket::categoryLabels()[$ticket->category] ?? $ticket->category }}</td></tr>
            <tr><td class="text-muted">ثبت‌کننده</td><td>{{ $ticket->user->name }}</td></tr>
            <tr><td class="text-muted">تاریخ</td><td>{{ $ticket->created_at->format('Y-m-d') }}</td></tr>
          </table>
          <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary w-100 mt-2">بازگشت به لیست</a>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      {{-- شرح اولیه --}}
      <div class="card mb-3 border-start border-4 border-primary">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <strong>{{ $ticket->user->name }}</strong>
            <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
          </div>
          <p class="mb-0">{{ $ticket->description }}</p>
        </div>
      </div>

      {{-- پاسخ‌ها --}}
      @foreach($ticket->replies as $reply)
      <div class="card mb-2 border-start border-4 border-{{ $reply->is_staff ? 'success' : 'info' }}">
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>
              <strong>{{ $reply->user->name }}</strong>
              @if($reply->is_staff)<span class="badge bg-label-success ms-1">کارشناس</span>@endif
            </span>
            <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
          </div>
          <p class="mb-0">{{ $reply->body }}</p>
        </div>
      </div>
      @endforeach

      {{-- فرم پاسخ --}}
      @if($ticket->canReply())
      <div class="card mt-3">
        <div class="card-body">
          <form action="{{ route('tickets.reply', $ticket) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">پاسخ شما</label>
              <textarea name="body" rows="4" class="form-control @error('body')is-invalid@enderror" required>{{ old('body') }}</textarea>
              @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-primary">ارسال پاسخ</button>
            </div>
          </form>
        </div>
      </div>
      @else
      <div class="alert alert-secondary mt-3">این تیکت بسته شده است.</div>
      @endif
    </div>
  </div>
</div>
@endsection
