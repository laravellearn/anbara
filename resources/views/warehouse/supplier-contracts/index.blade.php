@extends('layouts.warehouse')
@section('title', 'قراردادهای تأمین‌کنندگان')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-contract me-2 text-purple" style="color:#7367f0"></i>قراردادهای تأمین‌کنندگان</h4>
    @can('access','supplier-contracts.create')
    <a href="{{ route('warehouse.supplier-contracts.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> قرارداد جدید
    </a>
    @endcan
  </div>

  {{-- KPI --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold">{{ $stats['total'] }}</div>
          <div class="small text-muted">کل قراردادها</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-success">{{ $stats['active'] }}</div>
          <div class="small text-muted">فعال</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center border-warning">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-warning">{{ $stats['expiring'] }}</div>
          <div class="small text-muted">رو به انقضا (۳۰ روز)</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-danger">{{ $stats['expired'] }}</div>
          <div class="small text-muted">منقضی</div>
        </div>
      </div>
    </div>
  </div>

  {{-- فیلتر --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه وضعیت‌ها</option>
            @foreach(['draft'=>'پیش‌نویس','active'=>'فعال','expired'=>'منقضی','terminated'=>'فسخ‌شده'] as $v=>$l)
              <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <select name="supplier_id" class="form-select form-select-sm">
            <option value="">همه تأمین‌کنندگان</option>
            @foreach($suppliers as $s)
              <option value="{{ $s->id }}" @selected(request('supplier_id')==$s->id)>{{ $s->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="شماره / عنوان..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button class="btn btn-sm btn-primary flex-fill">اعمال</button>
          <a href="{{ route('warehouse.supplier-contracts.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-redo"></i></a>
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
              <th>شماره قرارداد</th>
              <th>عنوان</th>
              <th>تأمین‌کننده</th>
              <th>تاریخ شروع</th>
              <th>تاریخ پایان</th>
              <th class="text-end">سقف اعتبار</th>
              <th class="text-center">شرایط پرداخت</th>
              <th class="text-center">وضعیت</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($contracts as $c)
            @php $daysLeft = now()->diffInDays($c->end_date, false); @endphp
            <tr class="{{ $daysLeft <= 30 && $daysLeft >= 0 && $c->status === 'active' ? 'table-warning' : '' }}">
              <td class="fw-medium">{{ $c->contract_number }}</td>
              <td>{{ $c->title }}</td>
              <td><span class="fw-medium">{{ $c->supplier?->name }}</span></td>
              <td><small>{{ \Morilog\Jalali\Jalalian::fromCarbon($c->start_date)->format('Y/m/d') }}</small></td>
              <td>
                <small class="{{ $daysLeft <= 30 && $daysLeft >= 0 ? 'text-warning fw-bold' : '' }}">
                  {{ \Morilog\Jalali\Jalalian::fromCarbon($c->end_date)->format('Y/m/d') }}
                  @if($daysLeft >= 0 && $daysLeft <= 30 && $c->status === 'active')
                    <span class="badge bg-warning-subtle text-warning ms-1">{{ $daysLeft }} روز مانده</span>
                  @endif
                </small>
              </td>
              <td class="text-end">{{ number_format($c->credit_limit) }} ﷼</td>
              <td class="text-center"><small>{{ $c->payment_terms_days }} روز</small></td>
              <td class="text-center">
                <span class="badge bg-{{ $c->status_color }}-subtle text-{{ $c->status_color }}">{{ $c->status_label }}</span>
              </td>
              <td class="text-center">
                <a href="{{ route('warehouse.supplier-contracts.show', $c) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="fas fa-eye"></i></a>
                @can('access','supplier-contracts.create')
                <a href="{{ route('warehouse.supplier-contracts.edit', $c) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="ویرایش"><i class="fas fa-edit"></i></a>
                @endcan
              </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-5">قراردادی یافت نشد.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $contracts->links() }}</div>
    </div>
  </div>
</div>
@endsection
