@extends('layouts.warehouse')
@section('title', 'انتقال بین انبارها')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-exchange-alt me-2 text-primary"></i>انتقال بین انبارها</h4>
    @can('access','transfers.create')
    <a href="{{ route('warehouse.transfers.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> سند انتقال جدید
    </a>
    @endcan
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-4">
    @foreach([['total','کل اسناد','secondary','exchange-alt'],['draft','پیش‌نویس','warning','file-alt'],['in_transit','در حال انتقال','info','truck'],['completed','تکمیل‌شده','success','check-circle']] as [$key,$label,$color,$icon])
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-{{ $color }}">{{ $stats[$key] }}</div>
          <div class="small text-muted"><i class="fas fa-{{ $icon }} me-1"></i>{{ $label }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(['draft'=>'پیش‌نویس','confirmed'=>'تأیید شده','in_transit'=>'در حال انتقال','completed'=>'تکمیل شده','cancelled'=>'لغو شده'] as $val=>$lbl)
              <option value="{{ $val }}" @selected(request('status')===$val)>{{ $lbl }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="warehouse_id" class="form-select form-select-sm">
            <option value="">همه انبارها</option>
            @foreach($warehouses as $wh)
              <option value="{{ $wh->id }}" @selected(request('warehouse_id')==$wh->id)>{{ $wh->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="شماره سند..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-sm btn-primary flex-fill">اعمال</button>
          <a href="{{ route('warehouse.transfers.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-redo"></i></a>
        </div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>شماره سند</th>
              <th>از انبار</th>
              <th>به انبار</th>
              <th>تاریخ</th>
              <th class="text-center">وضعیت</th>
              <th>ایجادکننده</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($transfers as $t)
            <tr>
              <td class="fw-medium">{{ $t->transfer_number }}</td>
              <td>{{ $t->fromWarehouse?->title }}</td>
              <td>{{ $t->toWarehouse?->title }}</td>
              <td><small>{{ \Morilog\Jalali\Jalalian::fromCarbon($t->transfer_date)->format('Y/m/d') }}</small></td>
              <td class="text-center">
                <span class="badge bg-{{ $t->status_color }}-subtle text-{{ $t->status_color }}">{{ $t->status_label }}</span>
              </td>
              <td><small class="text-muted">{{ $t->creator?->name }}</small></td>
              <td class="text-center">
                <a href="{{ route('warehouse.transfers.show', $t) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده">
                  <i class="fas fa-eye"></i>
                </a>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">سند انتقالی یافت نشد.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $transfers->links() }}</div>
    </div>
  </div>
</div>
@endsection
