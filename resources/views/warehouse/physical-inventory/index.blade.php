@extends('layouts.warehouse')
@section('title', 'انبارگردانی')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2 text-info"></i>انبارگردانی</h4>
    @can('access','physical-inventory.create')
    <a href="{{ route('warehouse.physical-inventory.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> انبارگردانی جدید
    </a>
    @endcan
  </div>

  <div class="row g-3 mb-4">
    @foreach([['total','کل','secondary'],['counting','در حال شمارش','info'],['adjusted','تعدیل‌شده','success']] as [$k,$l,$c])
    <div class="col-4 col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body py-3">
          <div class="fs-3 fw-bold text-{{ $c }}">{{ $stats[$k] }}</div>
          <div class="small text-muted">{{ $l }}</div>
        </div>
      </div>
    </div>
    @endforeach
  </div>

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-3">
          <select name="status" class="form-select form-select-sm">
            <option value="">همه</option>
            @foreach(['draft'=>'پیش‌نویس','counting'=>'در حال شمارش','completed'=>'شمارش کامل','adjusted'=>'تعدیل‌شده','cancelled'=>'لغو'] as $v=>$l)
              <option value="{{ $v }}" @selected(request('status')===$v)>{{ $l }}</option>
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
        <div class="col-md-2 d-flex gap-2">
          <button class="btn btn-sm btn-primary flex-fill">اعمال</button>
          <a href="{{ route('warehouse.physical-inventory.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-redo"></i></a>
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
              <th>شماره</th>
              <th>انبار</th>
              <th>تاریخ</th>
              <th class="text-center">وضعیت</th>
              <th class="text-center">تعداد اقلام</th>
              <th>ایجادکننده</th>
              <th class="text-center">عملیات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($inventories as $inv)
            <tr>
              <td class="fw-medium">{{ $inv->inventory_number }}</td>
              <td>{{ $inv->warehouse?->title }}</td>
              <td><small>{{ \Morilog\Jalali\Jalalian::fromCarbon($inv->inventory_date)->format('Y/m/d') }}</small></td>
              <td class="text-center">
                <span class="badge bg-{{ $inv->status_color }}-subtle text-{{ $inv->status_color }}">{{ $inv->status_label }}</span>
              </td>
              <td class="text-center">{{ $inv->items_count ?? 0 }}</td>
              <td><small class="text-muted">{{ $inv->creator?->name }}</small></td>
              <td class="text-center">
                <a href="{{ route('warehouse.physical-inventory.show', $inv) }}" class="btn btn-sm btn-icon btn-outline-primary"><i class="fas fa-eye"></i></a>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-5">انبارگردانی یافت نشد.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-3">{{ $inventories->links() }}</div>
    </div>
  </div>
</div>
@endsection
