@extends('layouts.app')
@section('title', 'تیکت‌های پشتیبانی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- KPIs --}}
  <div class="row g-3 mb-4">
    @foreach([['باز','open','danger','message-circle'],['در جریان','progress','warning','clock'],['حل شده','resolved','success','check-circle'],['کل','total','primary','inbox']] as [$label,$key,$color,$icon])
    <div class="col-6 col-xl-3">
      <div class="card text-center h-100">
        <div class="card-body">
          <span class="avatar bg-label-{{ $color }} rounded mb-2 d-inline-flex"><i data-feather="{{ $icon }}"></i></span>
          <h6 class="text-muted mb-1">{{ $label }}</h6>
          <h3 class="mb-0 text-{{ $color }}">{{ number_format($stats[$key]) }}</h3>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- فیلتر --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(\App\Models\Ticket::statusLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="priority" class="form-select form-select-sm">
            <option value="">همه اولویت‌ها</option>
            @foreach(\App\Models\Ticket::priorityLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('priority')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="category" class="form-select form-select-sm">
            <option value="">همه دسته‌بندی‌ها</option>
            @foreach(\App\Models\Ticket::categoryLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-primary btn-sm flex-fill">فیلتر</button>
          <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm">پاک</a>
          <a href="{{ route('tickets.create') }}" class="btn btn-success btn-sm">+ تیکت جدید</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle table-hover">
        <thead class="table-light">
          <tr><th>#</th><th>شماره</th><th>موضوع</th><th>دسته</th><th>اولویت</th><th>وضعیت</th><th>تاریخ</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($tickets as $t)
          @php
            $sc = \App\Models\Ticket::statusColors(); $sl = \App\Models\Ticket::statusLabels();
            $pc = \App\Models\Ticket::priorityColors(); $pl = \App\Models\Ticket::priorityLabels();
            $cl = \App\Models\Ticket::categoryLabels();
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td><a href="{{ route('tickets.show', $t) }}">{{ $t->ticket_number }}</a></td>
            <td>{{ Str::limit($t->subject, 50) }}</td>
            <td><span class="badge bg-label-secondary">{{ $cl[$t->category] ?? $t->category }}</span></td>
            <td><span class="badge bg-label-{{ $pc[$t->priority]??'secondary' }}">{{ $pl[$t->priority]??$t->priority }}</span></td>
            <td><span class="badge bg-label-{{ $sc[$t->status]??'secondary' }}">{{ $sl[$t->status]??$t->status }}</span></td>
            <td>{{ $t->created_at->diffForHumans() }}</td>
            <td><a href="{{ route('tickets.show', $t) }}" class="btn btn-xs btn-icon btn-outline-primary"><i data-feather="eye"></i></a></td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">تیکتی ثبت نشده است.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())<div class="card-footer">{{ $tickets->links() }}</div>@endif
  </div>
</div>
@endsection
