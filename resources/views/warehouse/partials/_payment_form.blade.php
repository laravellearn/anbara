{{--
  _payment_form.blade.php
  پارشال ثبت پرداخت — قابل استفاده در show فاکتور فروش و خرید
  متغیرهای مورد نیاز:
    $invoice  — SalesInvoice یا PurchaseInvoice
    $type     — 'sales' | 'purchase'
--}}
@php
  $routeName = $type === 'sales'
    ? 'warehouse.invoice-payments.store-sales'
    : 'warehouse.invoice-payments.store-purchase';
  $remaining = $type === 'sales'
    ? ($invoice->total_amount - $invoice->paid_amount)
    : ($invoice->items->sum(fn($i) => $i->quantity * $i->unit_price * (1 - $i->discount_percent / 100)) - $invoice->invoicePayments->sum('amount'));
@endphp

<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
    <span class="fw-semibold"><i class="fas fa-money-bill-wave me-1 text-success"></i> تسویه و پرداخت</span>
    <span class="badge bg-{{ $remaining > 0 ? 'warning' : 'success' }}-subtle text-{{ $remaining > 0 ? 'warning' : 'success' }}">
      مانده: {{ number_format($remaining) }} ریال
    </span>
  </div>

  {{-- لیست پرداخت‌های قبلی --}}
  @if($invoice->invoicePayments->count())
  <div class="table-responsive border-bottom">
    <table class="table table-sm align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>تاریخ</th><th>مبلغ</th><th>روش</th><th>مرجع</th><th>ثبت‌کننده</th><th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice->invoicePayments as $payment)
        <tr>
          <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
          <td class="fw-medium text-success">{{ number_format($payment->amount) }}</td>
          <td>{{ $payment->method_label }}</td>
          <td>{{ $payment->reference_number ?? '—' }}</td>
          <td>{{ $payment->creator?->name ?? '—' }}</td>
          <td>
            @can('access', 'invoice-payments.delete')
            <form action="{{ route('warehouse.invoice-payments.destroy', $payment) }}" method="POST"
              onsubmit="return confirm('حذف این پرداخت؟')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1">
                <i class="fas fa-trash fa-xs"></i>
              </button>
            </form>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot class="table-light">
        <tr>
          <td class="fw-semibold">جمع پرداخت‌ها:</td>
          <td class="fw-bold text-success" colspan="5">{{ number_format($invoice->invoicePayments->sum('amount')) }} ریال</td>
        </tr>
      </tfoot>
    </table>
  </div>
  @endif

  {{-- فرم ثبت پرداخت جدید --}}
  @can('access', 'invoice-payments.create')
  @if(!in_array($invoice->status, ['cancelled', 'paid']))
  <div class="card-body">
    <p class="small text-muted mb-3">ثبت پرداخت جدید</p>
    <form action="{{ route($routeName, $invoice) }}" method="POST">
      @csrf
      <div class="row g-2">
        <div class="col-md-2">
          <label class="form-label small">تاریخ پرداخت <span class="text-danger">*</span></label>
          <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ now()->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-2">
          <label class="form-label small">مبلغ (ریال) <span class="text-danger">*</span></label>
          <input type="number" name="amount" class="form-control form-control-sm" step="0.01" min="0.01"
            value="{{ max(0, $remaining) }}" required>
        </div>
        <div class="col-md-2">
          <label class="form-label small">روش پرداخت <span class="text-danger">*</span></label>
          <select name="payment_method" class="form-select form-select-sm" required id="pmMethod{{ $type }}">
            <option value="bank_transfer">انتقال بانکی</option>
            <option value="cash">نقدی</option>
            <option value="cheque">چک</option>
            <option value="card">کارت به کارت</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">شماره مرجع / چک</label>
          <input type="text" name="reference_number" class="form-control form-control-sm" placeholder="اختیاری">
        </div>
        <div class="col-md-2 cheque-fields-{{ $type }} d-none">
          <label class="form-label small">تاریخ سررسید چک</label>
          <input type="date" name="cheque_date" class="form-control form-control-sm">
        </div>
        <div class="col-md-2 cheque-fields-{{ $type }} d-none">
          <label class="form-label small">نام بانک</label>
          <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="اختیاری">
        </div>
        <div class="col-12">
          <label class="form-label small">توضیحات</label>
          <input type="text" name="notes" class="form-control form-control-sm" placeholder="اختیاری">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-sm btn-success">
            <i class="fas fa-plus me-1"></i> ثبت پرداخت
          </button>
        </div>
      </div>
    </form>
  </div>
  @endif
  @endcan
</div>

@push('scripts')
<script>
document.getElementById('pmMethod{{ $type }}').addEventListener('change', function() {
  const chequeFields = document.querySelectorAll('.cheque-fields-{{ $type }}');
  chequeFields.forEach(f => f.classList.toggle('d-none', this.value !== 'cheque'));
});
</script>
@endpush
