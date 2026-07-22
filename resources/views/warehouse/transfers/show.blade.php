@extends('layouts.warehouse')
@section('title', 'سند انتقال '.$transfer->transfer_number)

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('warehouse.transfers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
      <h4 class="mb-0 fw-bold">{{ $transfer->transfer_number }}</h4>
      <span class="badge bg-{{ $transfer->status_color }}-subtle text-{{ $transfer->status_color }} fs-6">{{ $transfer->status_label }}</span>
    </div>
    <div class="d-flex gap-2">
      @can('access','transfers.confirm')
        @if($transfer->canConfirm())
          <form method="POST" action="{{ route('warehouse.transfers.confirm', $transfer) }}">@csrf
            <button class="btn btn-primary btn-sm"><i class="fas fa-check me-1"></i>تأیید</button>
          </form>
        @endif
        @if($transfer->canTransit())
          <form method="POST" action="{{ route('warehouse.transfers.transit', $transfer) }}">@csrf
            <button class="btn btn-info btn-sm text-white"><i class="fas fa-truck me-1"></i>شروع حمل</button>
          </form>
        @endif
        @if($transfer->canComplete())
          <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#completeModal">
            <i class="fas fa-flag-checkered me-1"></i>تکمیل
          </button>
        @endif
      @endcan
      @can('access','transfers.cancel')
        @if($transfer->canCancel())
          <form method="POST" action="{{ route('warehouse.transfers.cancel', $transfer) }}"
            onsubmit="return confirm('آیا از لغو این سند مطمئن هستید؟')">@csrf
            <button class="btn btn-outline-danger btn-sm"><i class="fas fa-ban me-1"></i>لغو</button>
          </form>
        @endif
      @endcan
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      {{-- اطلاعات --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="text-muted small mb-1">انبار مبدأ</div>
              <div class="fw-medium">{{ $transfer->fromWarehouse?->title }}</div>
            </div>
            <div class="col-md-6">
              <div class="text-muted small mb-1">انبار مقصد</div>
              <div class="fw-medium">{{ $transfer->toWarehouse?->title }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تاریخ انتقال</div>
              <div>{{ \Morilog\Jalali\Jalalian::fromCarbon($transfer->transfer_date)->format('Y/m/d') }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تاریخ تحویل پیش‌بینی‌شده</div>
              <div>{{ $transfer->expected_arrival_date ? \Morilog\Jalali\Jalalian::fromCarbon($transfer->expected_arrival_date)->format('Y/m/d') : '—' }}</div>
            </div>
            <div class="col-md-4">
              <div class="text-muted small mb-1">تاریخ تحویل واقعی</div>
              <div>{{ $transfer->actual_arrival_date ? \Morilog\Jalali\Jalalian::fromCarbon($transfer->actual_arrival_date)->format('Y/m/d') : '—' }}</div>
            </div>
            @if($transfer->reason)
            <div class="col-12">
              <div class="text-muted small mb-1">دلیل انتقال</div>
              <div>{{ $transfer->reason }}</div>
            </div>
            @endif
            @if($transfer->notes)
            <div class="col-12">
              <div class="text-muted small mb-1">یادداشت</div>
              <div class="text-muted">{{ $transfer->notes }}</div>
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- اقلام --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">اقلام انتقالی</div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>کالا</th>
                  <th>واحد</th>
                  <th class="text-center">درخواستی</th>
                  <th class="text-center">منتقل‌شده</th>
                  <th class="text-end">قیمت واحد</th>
                  <th class="text-end">ارزش کل</th>
                </tr>
              </thead>
              <tbody>
                @foreach($transfer->items as $i => $item)
                <tr>
                  <td class="text-muted small">{{ $i+1 }}</td>
                  <td class="fw-medium">{{ $item->product?->title }}</td>
                  <td><small class="text-muted">{{ $item->measurementUnit?->title ?? '—' }}</small></td>
                  <td class="text-center">{{ number_format($item->quantity_requested, 2) }}</td>
                  <td class="text-center {{ $item->quantity_transferred > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                    {{ number_format($item->quantity_transferred, 2) }}
                  </td>
                  <td class="text-end">{{ $item->unit_price ? number_format($item->unit_price) : '—' }}</td>
                  <td class="text-end">
                    {{ $item->unit_price ? number_format($item->quantity_transferred * $item->unit_price) : '—' }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- تایم‌لاین --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent fw-semibold">تاریخچه سند</div>
        <div class="card-body">
          <ul class="list-unstyled mb-0">
            <li class="mb-3 d-flex gap-2">
              <span class="badge bg-secondary-subtle text-secondary mt-1"><i class="fas fa-file-alt"></i></span>
              <div>
                <div class="fw-medium small">ایجاد سند</div>
                <div class="text-muted small">{{ $transfer->creator?->name }}</div>
                <div class="text-muted" style="font-size:.75rem">{{ \Morilog\Jalali\Jalalian::fromCarbon($transfer->created_at)->format('Y/m/d H:i') }}</div>
              </div>
            </li>
            @if($transfer->confirmer)
            <li class="mb-3 d-flex gap-2">
              <span class="badge bg-primary-subtle text-primary mt-1"><i class="fas fa-check"></i></span>
              <div>
                <div class="fw-medium small">تأیید</div>
                <div class="text-muted small">{{ $transfer->confirmer?->name }}</div>
                <div class="text-muted" style="font-size:.75rem">{{ \Morilog\Jalali\Jalalian::fromCarbon($transfer->confirmed_at)->format('Y/m/d H:i') }}</div>
              </div>
            </li>
            @endif
            @if($transfer->completer)
            <li class="mb-3 d-flex gap-2">
              <span class="badge bg-success-subtle text-success mt-1"><i class="fas fa-flag-checkered"></i></span>
              <div>
                <div class="fw-medium small">تکمیل</div>
                <div class="text-muted small">{{ $transfer->completer?->name }}</div>
                <div class="text-muted" style="font-size:.75rem">{{ \Morilog\Jalali\Jalalian::fromCarbon($transfer->completed_at)->format('Y/m/d H:i') }}</div>
              </div>
            </li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- مودال تکمیل --}}
@if($transfer->canComplete())
<div class="modal fade" id="completeModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">تکمیل انتقال</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('warehouse.transfers.complete', $transfer) }}">
        @csrf
        <div class="modal-body">
          <p class="text-muted mb-3">مقدار نهایی منتقل‌شده هر کالا را وارد کنید:</p>
          <table class="table table-sm">
            <thead><tr><th>کالا</th><th>درخواستی</th><th>منتقل‌شده</th></tr></thead>
            <tbody>
              @foreach($transfer->items as $item)
              <tr>
                <td>{{ $item->product?->title }}</td>
                <td>{{ number_format($item->quantity_requested, 2) }}</td>
                <td><input type="number" name="quantities[{{ $item->id }}]" class="form-control form-control-sm"
                  value="{{ $item->quantity_requested }}" min="0" step="0.0001" style="width:120px"></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">انصراف</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>تأیید تکمیل</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection
