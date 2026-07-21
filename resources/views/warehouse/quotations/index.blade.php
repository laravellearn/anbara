@extends('layouts.app')
@section('title','پیش‌فاکتورها')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row g-3 mb-4">
    @foreach([['کل','total','primary'],['پیش‌نویس','draft','secondary'],['ارسال شده','sent','info'],['پذیرفته','accepted','success']] as [$l,$k,$c])
    <div class="col-6 col-xl-3">
      <div class="card text-center"><div class="card-body">
        <h6 class="text-muted">{{ $l }}</h6><h3 class="text-{{ $c }}">{{ number_format($stats[$k]) }}</h3>
      </div></div>
    </div>
    @endforeach
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="شماره یا توضیح..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(\App\Models\Quotation::statusLabels() as $k=>$v)
              <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="customer_id" class="form-select form-select-sm">
            <option value="">همه مشتریان</option>
            @foreach($customers as $c)
              <option value="{{ $c->id }}" {{ request('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-primary btn-sm flex-fill">فیلتر</button>
          <a href="{{ route('warehouse.quotations.index') }}" class="btn btn-outline-secondary btn-sm">پاک</a>
          <a href="{{ route('warehouse.quotations.create') }}" class="btn btn-success btn-sm">+ جدید</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table align-middle table-hover">
        <thead class="table-light">
          <tr><th>شماره</th><th>تاریخ</th><th>مشتری</th><th class="text-end">مبلغ</th><th>اعتبار تا</th><th>وضعیت</th><th>عملیات</th></tr>
        </thead>
        <tbody>
          @forelse($quotations as $q)
          @php $colors=\App\Models\Quotation::statusColors();$labels=\App\Models\Quotation::statusLabels(); @endphp
          <tr>
            <td><a href="{{ route('warehouse.quotations.show',$q) }}">{{ $q->quotation_number }}</a></td>
            <td>{{ $q->quotation_date->format('Y-m-d') }}</td>
            <td>{{ $q->customer?->name ?? '—' }}</td>
            <td class="text-end">{{ number_format($q->total_amount) }}</td>
            <td>{{ $q->valid_until?->format('Y-m-d') ?? '—' }}</td>
            <td><span class="badge bg-label-{{ $colors[$q->status]??'secondary' }}">{{ $labels[$q->status]??$q->status }}</span></td>
            <td class="d-flex gap-1">
              <a href="{{ route('warehouse.quotations.show',$q) }}" class="btn btn-xs btn-icon btn-outline-primary"><i data-feather="eye"></i></a>
              @if($q->isEditable())
              <a href="{{ route('warehouse.quotations.edit',$q) }}" class="btn btn-xs btn-icon btn-outline-warning"><i data-feather="edit"></i></a>
              @endif
              <a href="{{ route('warehouse.quotations.print',$q) }}" target="_blank" class="btn btn-xs btn-icon btn-outline-secondary"><i data-feather="printer"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted py-4">پیش‌فاکتوری یافت نشد.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($quotations->hasPages())<div class="card-footer">{{ $quotations->links() }}</div>@endif
  </div>
</div>
@endsection
