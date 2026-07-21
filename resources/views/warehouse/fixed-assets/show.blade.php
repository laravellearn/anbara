@extends('layouts.master')
@section('title', 'جزئیات دارایی: ' . $fixedAsset->title)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- هدر --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body">
      <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
        <div class="d-flex align-items-center gap-3">
          <a href="{{ route('warehouse.fixed-assets.index') }}" class="btn btn-sm btn-icon btn-outline-secondary"><i class="bx bx-arrow-back"></i></a>
          <div>
            <h5 class="mb-0">{{ $fixedAsset->title }}</h5>
            <small class="text-muted">کد: {{ $fixedAsset->asset_code }}
              @if($fixedAsset->serial_number) &nbsp;|&nbsp; S/N: {{ $fixedAsset->serial_number }} @endif
            </small>
          </div>
          @php
            $sc = ['active'=>'success','assigned'=>'warning','under_maintenance'=>'danger','retired'=>'secondary','scrapped'=>'dark'][$fixedAsset->status] ?? 'secondary';
          @endphp
          <span class="badge bg-label-{{ $sc }} fs-6">{{ \App\Models\FixedAsset::STATUSES[$fixedAsset->status] }}</span>
        </div>
        <div class="d-flex gap-2">
          @can('access', 'fixed-assets.edit')
          <a href="{{ route('warehouse.fixed-assets.edit', $fixedAsset) }}" class="btn btn-outline-warning btn-sm"><i class="bx bx-edit me-1"></i> ویرایش</a>
          @endcan
          @can('access', 'fixed-assets.scrap')
          @if(!in_array($fixedAsset->status, ['scrapped','retired']))
          <form action="{{ route('warehouse.fixed-assets.scrap', $fixedAsset) }}" method="POST"
            onsubmit="return confirm('آیا از اسقاط این دارایی اطمینان دارید؟')">
            @csrf
            <button class="btn btn-outline-danger btn-sm"><i class="bx bx-trash me-1"></i> اسقاط</button>
          </form>
          @endif
          @endcan
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- اطلاعات پایه --}}
    <div class="col-md-4">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom"><h6 class="mb-0"><i class="bx bx-info-circle me-1"></i> اطلاعات دارایی</h6></div>
        <div class="card-body">
          <dl class="row mb-0">
            <dt class="col-6 text-muted small">دسته</dt>
            <dd class="col-6 small">{{ \App\Models\FixedAsset::CATEGORIES[$fixedAsset->category] ?? '—' }}</dd>
            <dt class="col-6 text-muted small">محل</dt>
            <dd class="col-6 small">{{ $fixedAsset->location ?? '—' }}</dd>
            <dt class="col-6 text-muted small">قیمت خرید</dt>
            <dd class="col-6 small">{{ number_format($fixedAsset->purchase_price) }} ﷼</dd>
            <dt class="col-6 text-muted small">ارزش جاری</dt>
            <dd class="col-6 small fw-bold text-primary">{{ number_format($fixedAsset->current_value) }} ﷼</dd>
            <dt class="col-6 text-muted small">تاریخ خرید</dt>
            <dd class="col-6 small">{{ $fixedAsset->purchase_date?->format('Y/m/d') ?? '—' }}</dd>
            <dt class="col-6 text-muted small">انقضای ضمانت</dt>
            <dd class="col-6 small {{ $fixedAsset->warranty_expiry?->isPast() ? 'text-danger' : '' }}">
              {{ $fixedAsset->warranty_expiry?->format('Y/m/d') ?? '—' }}
            </dd>
            <dt class="col-6 text-muted small">ثبت‌کننده</dt>
            <dd class="col-6 small">{{ $fixedAsset->createdBy?->name ?? '—' }}</dd>
          </dl>
          @if($fixedAsset->description)
          <hr>
          <p class="small mb-0">{{ $fixedAsset->description }}</p>
          @endif
        </div>
      </div>
    </div>

    {{-- تخصیص + عودت --}}
    <div class="col-md-8">
      <div class="card shadow-none border mb-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-user-check me-1"></i> تخصیص به پرسنل</h6>
        </div>
        <div class="card-body">
          @can('access', 'fixed-assets.assign')
          @if(!in_array($fixedAsset->status, ['scrapped','retired']))
          <form action="{{ route('warehouse.fixed-assets.assign', $fixedAsset) }}" method="POST" class="mb-3">
            @csrf
            <div class="row g-2 align-items-end">
              <div class="col-md-4">
                <label class="form-label form-label-sm">کارمند</label>
                <select name="employee_id" class="form-select form-select-sm" required>
                  <option value="">انتخاب کارمند...</option>
                  @foreach($employees as $emp)
                  <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label form-label-sm">تاریخ تخصیص</label>
                <input type="date" name="assigned_at" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-3">
                <label class="form-label form-label-sm">توضیحات</label>
                <input type="text" name="notes" class="form-control form-control-sm" placeholder="اختیاری">
              </div>
              <div class="col-md-2">
                <button class="btn btn-primary btn-sm w-100"><i class="bx bx-check me-1"></i> تخصیص</button>
              </div>
            </div>
          </form>
          @endif
          @endcan

          {{-- تاریخچه تخصیص --}}
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>کارمند</th><th>از تاریخ</th><th>تا تاریخ</th><th>وضعیت</th><th>توسط</th><th></th></tr>
              </thead>
              <tbody>
                @forelse($fixedAsset->assignments as $asgn)
                <tr>
                  <td>{{ $asgn->employee?->name ?? '—' }}</td>
                  <td><small>{{ $asgn->assigned_at->format('Y/m/d') }}</small></td>
                  <td><small>{{ $asgn->returned_at?->format('Y/m/d') ?? '—' }}</small></td>
                  <td>
                    <span class="badge bg-label-{{ $asgn->status === 'active' ? 'warning' : 'secondary' }}">
                      {{ $asgn->status === 'active' ? 'فعال' : 'عودت‌شده' }}
                    </span>
                  </td>
                  <td><small>{{ $asgn->assignedBy?->name ?? '—' }}</small></td>
                  <td>
                    @if($asgn->status === 'active')
                    @can('access', 'fixed-assets.assign')
                    <form action="{{ route('warehouse.fixed-assets.return', $fixedAsset) }}" method="POST" class="d-inline">
                      @csrf
                      <input type="hidden" name="returned_at" value="{{ date('Y-m-d') }}">
                      <button class="btn btn-xs btn-outline-secondary" onclick="return confirm('ثبت عودت دارایی؟')">
                        <small>عودت</small>
                      </button>
                    </form>
                    @endcan
                    @endif
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-2">تخصیصی ثبت نشده</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- تعمیر و نگهداری --}}
      <div class="card shadow-none border">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0"><i class="bx bx-wrench me-1"></i> تعمیر و نگهداری</h6>
        </div>
        <div class="card-body">
          @can('access', 'fixed-assets.maintain')
          <form action="{{ route('warehouse.fixed-assets.maintenance', $fixedAsset) }}" method="POST" class="mb-3">
            @csrf
            <div class="row g-2 align-items-end">
              <div class="col-md-2">
                <label class="form-label form-label-sm">نوع</label>
                <select name="type" class="form-select form-select-sm" required>
                  @foreach(\App\Models\FixedAssetMaintenance::TYPES as $v => $l)
                  <option value="{{ $v }}">{{ $l }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label form-label-sm">تاریخ</label>
                <input type="date" name="maintenance_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
              </div>
              <div class="col-md-2">
                <label class="form-label form-label-sm">هزینه (ریال)</label>
                <input type="number" name="cost" class="form-control form-control-sm" min="0" value="0">
              </div>
              <div class="col-md-2">
                <label class="form-label form-label-sm">انجام‌دهنده</label>
                <input type="text" name="performed_by" class="form-control form-control-sm" placeholder="نام شرکت/فرد">
              </div>
              <div class="col-md-2">
                <label class="form-label form-label-sm">سرویس بعدی</label>
                <input type="date" name="next_maintenance_date" class="form-control form-control-sm">
              </div>
              <div class="col-md-2">
                <button class="btn btn-warning btn-sm w-100"><i class="bx bx-plus me-1"></i> ثبت</button>
              </div>
              <div class="col-12">
                <input type="text" name="description" class="form-control form-control-sm" placeholder="توضیحات تعمیر...">
              </div>
            </div>
          </form>
          @endcan

          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>تاریخ</th><th>نوع</th><th>انجام‌دهنده</th><th class="text-end">هزینه</th><th>سرویس بعدی</th><th>توضیحات</th></tr>
              </thead>
              <tbody>
                @forelse($fixedAsset->maintenances as $m)
                <tr>
                  <td><small>{{ $m->maintenance_date->format('Y/m/d') }}</small></td>
                  <td><span class="badge bg-label-info"><small>{{ \App\Models\FixedAssetMaintenance::TYPES[$m->type] ?? $m->type }}</small></span></td>
                  <td><small>{{ $m->performed_by ?? '—' }}</small></td>
                  <td class="text-end"><small>{{ number_format($m->cost) }}</small></td>
                  <td><small class="{{ $m->next_maintenance_date?->isPast() ? 'text-danger' : '' }}">{{ $m->next_maintenance_date?->format('Y/m/d') ?? '—' }}</small></td>
                  <td><small>{{ Str::limit($m->description, 40) ?? '—' }}</small></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-2">سابقه‌ای ثبت نشده</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
