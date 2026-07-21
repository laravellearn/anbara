@extends('layouts.master')
@section('title', 'خلاصه خرید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

  {{-- فیلتر تاریخ --}}
  <div class="card shadow-none border mb-4">
    <div class="card-body">
      <form method="GET" action="{{ route('warehouse.reports.purchase-summary') }}">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-medium">از تاریخ</label>
            <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-medium">تا تاریخ</label>
            <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bx bx-search me-1"></i> اعمال</button>
            <a href="{{ route('warehouse.reports.purchase-summary') }}" class="btn btn-outline-secondary"><i class="bx bx-reset"></i></a>
          </div>
          <div class="col-md-3">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-success w-100">
              <i class="bx bx-download me-1"></i> خروجی Excel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- فانل خرید --}}
  <div class="row g-4 mb-4">
    {{-- درخواست خرید --}}
    <div class="col-md-4">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom">
          <h6 class="mb-0"><i class="bx bx-cart-add me-1 text-info"></i> درخواست‌های خرید</h6>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">تعداد کل</span>
            <span class="fw-bold">{{ $totals['pr_count'] }}</span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">مجموع ارزش</span>
            <span class="fw-bold text-info">{{ number_format($totals['pr_total']) }} ﷼</span>
          </div>
          <hr>
          @foreach(['draft'=>'پیش‌نویس','submitted'=>'ارسال‌شده','approved'=>'تأیید‌شده','rejected'=>'رد‌شده','converted'=>'تبدیل به PO'] as $st => $label)
          @if(isset($prStats[$st]))
          <div class="d-flex justify-content-between mb-1">
            <small>{{ $label }}</small>
            <span class="badge bg-label-secondary">{{ $prStats[$st]->cnt }}</span>
          </div>
          @endif
          @endforeach
        </div>
      </div>
    </div>

    {{-- سفارش خرید --}}
    <div class="col-md-4">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom">
          <h6 class="mb-0"><i class="bx bx-file me-1 text-warning"></i> سفارشات خرید</h6>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">تعداد کل</span>
            <span class="fw-bold">{{ $totals['po_count'] }}</span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">مجموع ارزش</span>
            <span class="fw-bold text-warning">{{ number_format($totals['po_total']) }} ﷼</span>
          </div>
          <hr>
          @foreach(['draft'=>'پیش‌نویس','confirmed'=>'تأیید‌شده','sent'=>'ارسال‌شده','received'=>'تحویل‌گرفته','cancelled'=>'لغو‌شده'] as $st => $label)
          @if(isset($poStats[$st]))
          <div class="d-flex justify-content-between mb-1">
            <small>{{ $label }}</small>
            <span class="badge bg-label-secondary">{{ $poStats[$st]->cnt }}</span>
          </div>
          @endif
          @endforeach
        </div>
      </div>
    </div>

    {{-- فاکتور خرید --}}
    <div class="col-md-4">
      <div class="card shadow-none border h-100">
        <div class="card-header border-bottom">
          <h6 class="mb-0"><i class="bx bx-receipt me-1 text-success"></i> فاکتورهای خرید</h6>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">تعداد کل</span>
            <span class="fw-bold">{{ $totals['inv_count'] }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">جمع ارزش</span>
            <span class="fw-bold text-success">{{ number_format($totals['inv_total']) }} ﷼</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">پرداخت‌شده</span>
            <span class="fw-bold text-success">{{ number_format($totals['inv_paid']) }} ﷼</span>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <span class="text-muted">مانده پرداخت</span>
            <span class="fw-bold text-danger">{{ number_format($totals['inv_unpaid']) }} ﷼</span>
          </div>
          <hr>
          @foreach(['draft'=>'پیش‌نویس','registered'=>'ثبت‌شده','paid'=>'پرداخت‌شده','cancelled'=>'لغو‌شده'] as $st => $label)
          @if(isset($invoiceStats[$st]))
          <div class="d-flex justify-content-between mb-1">
            <small>{{ $label }}</small>
            <span class="badge bg-label-secondary">{{ $invoiceStats[$st]->cnt }}</span>
          </div>
          @endif
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- جدول فاکتورها --}}
  <div class="card shadow-none border">
    <div class="card-header border-bottom">
      <h5 class="card-title mb-0"><i class="bx bx-receipt me-1"></i> ریز فاکتورهای خرید</h5>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>شماره فاکتور</th>
            <th>تاریخ</th>
            <th>تامین‌کننده</th>
            <th>شماره PO</th>
            <th class="text-end">مبلغ (ریال)</th>
            <th class="text-end">پرداخت‌شده</th>
            <th class="text-end">مانده</th>
            <th>وضعیت</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($invoices as $inv)
          @php
            $remaining = $inv->total_amount - $inv->paid_amount;
            $statusMap = ['draft'=>['label'=>'پیش‌نویس','color'=>'secondary'],'registered'=>['label'=>'ثبت‌شده','color'=>'info'],'paid'=>['label'=>'پرداخت‌شده','color'=>'success'],'cancelled'=>['label'=>'لغو‌شده','color'=>'danger']];
            $st = $statusMap[$inv->status] ?? ['label'=>$inv->status,'color'=>'secondary'];
          @endphp
          <tr>
            <td class="fw-medium">{{ $inv->invoice_number }}</td>
            <td><small>{{ $inv->invoice_date }}</small></td>
            <td>{{ $inv->supplier_name ?? '—' }}</td>
            <td><small class="text-muted">{{ $inv->po_number ?? '—' }}</small></td>
            <td class="text-end">{{ number_format($inv->total_amount) }}</td>
            <td class="text-end text-success">{{ number_format($inv->paid_amount) }}</td>
            <td class="text-end {{ $remaining > 0 ? 'text-danger fw-bold' : 'text-muted' }}">{{ number_format($remaining) }}</td>
            <td><span class="badge bg-label-{{ $st['color'] }}">{{ $st['label'] }}</span></td>
            <td>
              <a href="{{ route('warehouse.purchase-invoices.show', $inv->id) }}"
                 class="btn btn-sm btn-icon btn-outline-primary"><i class="bx bx-show"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="9" class="text-center text-muted py-5">فاکتوری در این بازه ثبت نشده است.</td></tr>
          @endforelse
        </tbody>
        @if($invoices->count())
        <tfoot class="table-light fw-bold">
          <tr>
            <td colspan="4">جمع کل</td>
            <td class="text-end">{{ number_format($totals['inv_total']) }}</td>
            <td class="text-end text-success">{{ number_format($totals['inv_paid']) }}</td>
            <td class="text-end text-danger">{{ number_format($totals['inv_unpaid']) }}</td>
            <td colspan="2"></td>
          </tr>
        </tfoot>
        @endif
      </table>
    </div>
  </div>
</div>
@endsection
