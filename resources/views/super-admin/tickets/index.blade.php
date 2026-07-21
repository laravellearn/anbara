@extends('super-admin.layouts.master')
@section('title', 'مدیریت تیکت‌ها')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-3 mb-4">
    @foreach([['باز','open','danger'],['در انتظار کاربر','waiting','warning'],['حل شده','resolved','success'],['کل','total','primary']] as [$label,$key,$color])
    <div class="col-6 col-xl-3">
      <div class="card text-center"><div class="card-body">
        <h6 class="text-muted">{{ $label }}</h6>
        <h3 class="text-{{ $color }}">{{ number_format($stats[$key]) }}</h3>
      </div></div>
    </div>
    @endforeach
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2">
        <div class="col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(\App\Models\Ticket::statusLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="priority" class="form-select form-select-sm">
            <option value="">همه اولویت‌ها</option>
            @foreach(\App\Models\Ticket::priorityLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('priority')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <select name="category" class="form-select form-select-sm">
            <option value="">همه دسته‌ها</option>
            @foreach(\App\Models\Ticket::categoryLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('category')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary btn-sm w-100">فیلتر</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle table-hover">
        <thead class="table-light">
          <tr><th>شماره</th><th>سازمان</th><th>موضوع</th><th>اولویت</th><th>وضعیت</th><th>کارشناس</th><th>تاریخ</th><th></th></tr>
        </thead>
        <tbody>
          @forelse($tickets as $t)
          @php $sc=\App\Models\Ticket::statusColors();$sl=\App\Models\Ticket::statusLabels();$pc=\App\Models\Ticket::priorityColors();$pl=\App\Models\Ticket::priorityLabels(); @endphp
          <tr>
            <td><a href="{{ route('super-admin.tickets.show',$t) }}">{{ $t->ticket_number }}</a></td>
            <td>{{ $t->tenant?->name ?? '—' }}</td>
            <td>{{ Str::limit($t->subject,45) }}</td>
            <td><span class="badge bg-label-{{ $pc[$t->priority]??'secondary' }}">{{ $pl[$t->priority]??$t->priority }}</span></td>
            <td><span class="badge bg-label-{{ $sc[$t->status]??'secondary' }}">{{ $sl[$t->status]??$t->status }}</span></td>
            <td>{{ $t->assignedUser?->name ?? '—' }}</td>
            <td>{{ $t->created_at->diffForHumans() }}</td>
            <td><a href="{{ route('super-admin.tickets.show',$t) }}" class="btn btn-xs btn-icon btn-outline-primary"><i data-feather="eye"></i></a></td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center text-muted py-4">تیکتی یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($tickets->hasPages())<div class="card-footer">{{ $tickets->links() }}</div>@endif
  </div>
</div>
@endsection
