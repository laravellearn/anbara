@extends('layouts.master')
@section('title', 'دارایی‌های ثابت')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- KPI --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">کل دارایی‌ها</span><h3 class="mb-0 mt-1">{{ $stats['total'] }}</h3></div>
          <span class="badge bg-label-primary rounded p-2"><i class="bx bx-briefcase bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">تخصیص‌یافته</span><h3 class="mb-0 mt-1 text-warning">{{ $stats['assigned'] }}</h3></div>
          <span class="badge bg-label-warning rounded p-2"><i class="bx bx-user-check bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">در تعمیر</span><h3 class="mb-0 mt-1 text-danger">{{ $stats['under_maintenance'] }}</h3></div>
          <span class="badge bg-label-danger rounded p-2"><i class="bx bx-wrench bx-sm"></i></span>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card shadow-none border">
        <div class="card-body d-flex justify-content-between align-items-start">
          <div><span class="fw-medium text-muted">جمع ارزش جاری</span><h3 class="mb-0 mt-1 text-success" style="font-size:1rem">{{ number_format($stats['total_value']) }} ﷼</h3></div>
          <span class="badge bg-label-success rounded p-2"><i class="bx bx-dollar-circle bx-sm"></i></span>
        </div>
      </div>
    </div>
  </div>

  {{-- فیلترها --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('warehouse.fixed-assets.index') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-medium">جستجو</label>
            <input type="text" name="search" class="form-control" placeholder="عنوان، کد، سریال..." value="{{ request('search') }}">
          </div>
          <div class="col-md-2">
            <label class="form-label fw-medium">وضعیت</label>
            <select name="status" class="form-select">
              <option value="">همه</option>
              @foreach(\App\Models\FixedAsset::STATUSES as $val => $label)
              <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-medium">دسته</label>
            <select name="category" class="form-select">
              <option value="">همه</option>
              @foreach(\App\Models\FixedAsset::CATEGORIES as $val => $label)
              <option value="{{ $val }}" @selected(request('category') === $val)>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
            <a href="{{ route('warehouse.fixed-assets.index') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
          </div>
          @can('access', 'fixed-assets.create')
          <div class="col-md-2">
            <a href="{{ route('warehouse.fixed-assets.create') }}" class="btn btn-success w-100">
              <i class="bx bx-plus me-1"></i> دارایی جدید
            </a>
          </div>
          @endcan
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-none border">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><i class="bx bx-briefcase me-1"></i> لیست دارایی‌های ثابت</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>کد دارایی</th>
            <th>عنوان</th>
            <th>دسته</th>
            <th>محل</th>
            <th class="text-end">ارزش جاری</th>
            <th>تاریخ خرید</th>
            <th>متصدی فعلی</th>
            <th>وضعیت</th>
            <th>عملیات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($assets as $asset)
          @php
            $statusColors = ['active'=>'success','assigned'=>'warning','under_maintenance'=>'danger','retired'=>'secondary','scrapped'=>'dark'];
            $sc = $statusColors[$asset->status] ?? 'secondary';
          @endphp
          <tr>
            <td class="fw-medium">{{ $asset->asset_code }}</td>
            <td>
              <a href="{{ route('warehouse.fixed-assets.show', $asset) }}" class="text-body fw-medium">
                {{ $asset->title }}
              </a>
              @if($asset->serial_number)
              <br><small class="text-muted">S/N: {{ $asset->serial_number }}</small>
              @endif
            </td>
            <td><small>{{ \App\Models\FixedAsset::CATEGORIES[$asset->category] ?? $asset->category ?? '—' }}</small></td>
            <td><small>{{ $asset->location ?? '—' }}</small></td>
            <td class="text-end">{{ number_format($asset->current_value) }}</td>
            <td><small>{{ $asset->purchase_date?->format('Y/m/d') ?? '—' }}</small></td>
            <td>
              @if($asset->activeAssignment?->employee)
                <span class="badge bg-label-info">{{ $asset->activeAssignment->employee->name }}</span>
              @else
                <small class="text-muted">—</small>
              @endif
            </td>
            <td><span class="badge bg-label-{{ $sc }}">{{ \App\Models\FixedAsset::STATUSES[$asset->status] ?? $asset->status }}</span></td>
            <td>
              <div class="d-flex gap-1">
                <a href="{{ route('warehouse.fixed-assets.show', $asset) }}" class="btn btn-sm btn-icon btn-outline-primary" title="مشاهده"><i class="bx bx-show"></i></a>
                @can('access', 'fixed-assets.edit')
                <a href="{{ route('warehouse.fixed-assets.edit', $asset) }}" class="btn btn-sm btn-icon btn-outline-warning" title="ویرایش"><i class="bx bx-edit"></i></a>
                @endcan
                @can('access', 'fixed-assets.delete')
                <form action="{{ route('warehouse.fixed-assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('آیا از حذف اطمینان دارید؟')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-icon btn-outline-danger" title="حذف"><i class="bx bx-trash"></i></button>
                </form>
                @endcan
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-5">دارایی‌ای ثبت نشده است.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
      <small class="text-muted">نمایش {{ $assets->firstItem() ?? 0 }} تا {{ $assets->lastItem() ?? 0 }} از {{ $assets->total() }}</small>
      {{ $assets->links() }}
    </div>
  </div>
</div>
@endsection
