@extends('layouts.warehouse')
@section('title', $supplierContract->contract_number)

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('warehouse.supplier-contracts.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
      <h4 class="mb-0 fw-bold">{{ $supplierContract->contract_number }}</h4>
      <span class="badge bg-{{ $supplierContract->status_color }}-subtle text-{{ $supplierContract->status_color }} fs-6">{{ $supplierContract->status_label }}</span>
    </div>
    <div class="d-flex gap-2">
      @can('access','supplier-contracts.create')
      <a href="{{ route('warehouse.supplier-contracts.edit', $supplierContract) }}" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-edit me-1"></i>ویرایش
      </a>
      @if($supplierContract->status === 'active')
      <form method="POST" action="{{ route('warehouse.supplier-contracts.terminate', $supplierContract) }}"
        onsubmit="return confirm('قرارداد فسخ شود؟')">@csrf
        <button class="btn btn-outline-danger btn-sm"><i class="fas fa-times me-1"></i>فسخ قرارداد</button>
      </form>
      @endif
      @endcan
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent fw-semibold">اطلاعات قرارداد</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted small mb-1">تأمین‌کننده</div>
              <div class="fw-bold">{{ $supplierContract->supplier?->name }}</div>
              <small class="text-muted">{{ $supplierContract->supplier?->mobile }}</small>
            </div>
            <div class="col-md-6">
              <div class="text-muted small mb-1">عنوان</div>
              <div class="fw-medium">{{ $supplierContract->title }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تاریخ شروع</div>
              <div>{{ \Morilog\Jalali\Jalalian::fromCarbon($supplierContract->start_date)->format('Y/m/d') }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تاریخ پایان</div>
              @php $daysLeft = now()->diffInDays($supplierContract->end_date, false); @endphp
              <div class="{{ $daysLeft <= 30 && $daysLeft >= 0 ? 'text-warning fw-bold' : '' }}">
                {{ \Morilog\Jalali\Jalalian::fromCarbon($supplierContract->end_date)->format('Y/m/d') }}
                @if($daysLeft >= 0 && $daysLeft <= 30)
                  <span class="badge bg-warning-subtle text-warning ms-1">{{ $daysLeft }} روز مانده</span>
                @endif
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">شرایط پرداخت</div>
              <div>{{ $supplierContract->payment_terms_days }} روز</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">سقف اعتبار</div>
              <div class="fw-medium text-primary">{{ number_format($supplierContract->credit_limit) }} ﷼</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تخفیف قراردادی</div>
              <div>{{ $supplierContract->discount_percent }}%</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">ایجادکننده</div>
              <div>{{ $supplierContract->creator?->name }}</div>
            </div>
            @if($supplierContract->terms_and_conditions)
            <div class="col-12">
              <div class="text-muted small mb-1">شرایط و ضوابط</div>
              <div class="bg-light p-3 rounded" style="white-space:pre-wrap;font-size:.9rem">{{ $supplierContract->terms_and_conditions }}</div>
            </div>
            @endif
            @if($supplierContract->notes)
            <div class="col-12">
              <div class="text-muted small mb-1">یادداشت</div>
              <div class="text-muted">{{ $supplierContract->notes }}</div>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-semibold mb-3">خلاصه مالی</h6>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">سقف اعتبار</span>
            <span class="fw-bold text-primary">{{ number_format($supplierContract->credit_limit) }} ﷼</span>
          </div>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">تخفیف</span>
            <span>{{ $supplierContract->discount_percent }}%</span>
          </div>
          <div class="mb-2 d-flex justify-content-between">
            <span class="text-muted small">پرداخت</span>
            <span>{{ $supplierContract->payment_terms_days }} روز</span>
          </div>
          @if($supplierContract->file_path)
          <hr>
          <a href="{{ route('warehouse.supplier-contracts.download', $supplierContract) }}"
             class="btn btn-outline-secondary w-100 btn-sm">
            <i class="fas fa-download me-1"></i> دانلود فایل قرارداد
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
