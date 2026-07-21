@extends('layouts.warehouse')

@section('title', 'سند برگشت جدید')

@section('content')
<div class="container-fluid">

  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.return-invoices.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="fas fa-arrow-right"></i>
    </a>
    <div>
      <h4 class="mb-0 fw-bold">ثبت سند برگشت جدید</h4>
      <small class="text-muted">برگشت از فروش یا برگشت از خرید</small>
    </div>
  </div>

  <form action="{{ route('warehouse.return-invoices.store') }}" method="POST" id="returnForm">
    @csrf

    <div class="row g-4">
      {{-- سمت چپ: اطلاعات اصلی --}}
      <div class="col-lg-8">

        {{-- نوع برگشت --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">نوع سند برگشت</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">نوع برگشت <span class="text-danger">*</span></label>
                <select name="type" id="returnType" class="form-select @error('type') is-invalid @enderror" required>
                  <option value="sales"    {{ old('type', $sourceType) === 'sales'    ? 'selected' : '' }}>برگشت از فروش</option>
                  <option value="purchase" {{ old('type', $sourceType) === 'purchase' ? 'selected' : '' }}>برگشت از خرید</option>
                </select>
                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label">تاریخ برگشت <span class="text-danger">*</span></label>
                <input type="date" name="return_date" class="form-control @error('return_date') is-invalid @enderror"
                  value="{{ old('return_date', now()->format('Y-m-d')) }}" required>
                @error('return_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label">انبار <span class="text-danger">*</span></label>
                <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                  <option value="">انتخاب کنید</option>
                  @foreach($warehouses as $wh)
                  <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->title }}</option>
                  @endforeach
                </select>
                @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-3">
                <label class="form-label">سال مالی</label>
                <select name="fiscal_year_id" class="form-select">
                  <option value="">انتخاب کنید</option>
                  @foreach($fiscalYears as $fy)
                  <option value="{{ $fy->id }}" {{ old('fiscal_year_id') == $fy->id ? 'selected' : '' }}>{{ $fy->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">فاکتور مرجع (اختیاری)</label>
                <div id="salesInvoiceRef" class="{{ old('type', $sourceType) !== 'sales' ? 'd-none' : '' }}">
                  <select name="sales_invoice_id" class="form-select">
                    <option value="">انتخاب فاکتور فروش...</option>
                    @foreach($salesInvoices as $inv)
                    <option value="{{ $inv->id }}" {{ old('sales_invoice_id', $sourceInvoice?->id) == $inv->id ? 'selected' : '' }}>
                      {{ $inv->invoice_number }} — {{ $inv->customer?->name }}
                    </option>
                    @endforeach
                  </select>
                </div>
                <div id="purchaseInvoiceRef" class="{{ old('type', $sourceType) !== 'purchase' ? 'd-none' : '' }}">
                  <select name="purchase_invoice_id" class="form-select">
                    <option value="">انتخاب فاکتور خرید...</option>
                    @foreach($purchaseInvoices as $inv)
                    <option value="{{ $inv->id }}" {{ old('purchase_invoice_id', $sourceInvoice?->id) == $inv->id ? 'selected' : '' }}>
                      {{ $inv->invoice_number }} — {{ $inv->supplier?->name }}
                    </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">طرف حساب</label>
                <select name="contact_id" class="form-select">
                  <option value="">انتخاب کنید</option>
                  @foreach($contacts as $c)
                  <option value="{{ $c->id }}" {{ old('contact_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">دلیل برگشت</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="معیوب / اشتباه / ...">
              </div>
            </div>
          </div>
        </div>

        {{-- اقلام --}}
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">اقلام برگشتی</span>
            <button type="button" id="addItem" class="btn btn-sm btn-outline-primary">
              <i class="fas fa-plus me-1"></i> افزودن ردیف
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0" id="itemsTable">
                <thead class="table-light">
                  <tr>
                    <th width="30%">کالا</th>
                    <th width="15%">واحد</th>
                    <th width="15%">تعداد</th>
                    <th width="20%">قیمت واحد</th>
                    <th width="10%">تخفیف%</th>
                    <th width="10%"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  @if($sourceInvoice && $sourceInvoice->items->count())
                    @foreach($sourceInvoice->items as $i => $srcItem)
                    <tr class="item-row">
                      <td>
                        <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm" required>
                          @foreach($products as $p)
                          <option value="{{ $p->id }}" {{ $p->id == $srcItem->product_id ? 'selected' : '' }}>{{ $p->title }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td>
                        <select name="items[{{ $i }}][measurement_unit_id]" class="form-select form-select-sm">
                          <option value="">—</option>
                          @foreach($units as $u)
                          <option value="{{ $u->id }}" {{ $u->id == $srcItem->measurement_unit_id ? 'selected' : '' }}>{{ $u->title }}</option>
                          @endforeach
                        </select>
                      </td>
                      <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" value="{{ $srcItem->quantity }}" required></td>
                      <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm" step="0.01" min="0" value="{{ $srcItem->unit_price }}" required></td>
                      <td><input type="number" name="items[{{ $i }}][discount_percent]" class="form-control form-control-sm" step="0.01" min="0" max="100" value="{{ $srcItem->discount_percent ?? 0 }}"></td>
                      <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>
                    </tr>
                    @endforeach
                  @else
                  <tr class="item-row">
                    <td>
                      <select name="items[0][product_id]" class="form-select form-select-sm" required>
                        <option value="">انتخاب کالا...</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select name="items[0][measurement_unit_id]" class="form-select form-select-sm">
                        <option value="">—</option>
                        @foreach($units as $u)
                        <option value="{{ $u->id }}">{{ $u->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" required></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm" step="0.01" min="0" required></td>
                    <td><input type="number" name="items[0][discount_percent]" class="form-control form-control-sm" step="0.01" min="0" max="100" value="0"></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>

      {{-- سمت راست: جمع‌بندی --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">جمع‌بندی</div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label small">تخفیف کلی (ریال)</label>
              <input type="number" name="discount_amount" class="form-control" step="0.01" min="0" value="{{ old('discount_amount', 0) }}">
            </div>
            <div class="mb-3">
              <label class="form-label small">درصد مالیات</label>
              <input type="number" name="tax_percent" class="form-control" step="0.01" min="0" max="100" value="{{ old('tax_percent', 9) }}">
            </div>
            <div class="mb-3">
              <label class="form-label small">توضیحات</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> ذخیره سند برگشت
          </button>
          <a href="{{ route('warehouse.return-invoices.index') }}" class="btn btn-outline-secondary">انصراف</a>
        </div>
      </div>
    </div>

  </form>
</div>

@push('scripts')
<script>
let itemIdx = {{ $sourceInvoice ? $sourceInvoice->items->count() : 1 }};
const products = @json($products->map(fn($p) => ['id'=>$p->id,'title'=>$p->title]));
const units    = @json($units->map(fn($u)    => ['id'=>$u->id,'title'=>$u->title]));

document.getElementById('addItem').addEventListener('click', function() {
  const tbody = document.getElementById('itemsBody');
  const productOpts = products.map(p => `<option value="${p.id}">${p.title}</option>`).join('');
  const unitOpts    = units.map(u => `<option value="${u.id}">${u.title}</option>`).join('');
  const tr = document.createElement('tr');
  tr.className = 'item-row';
  tr.innerHTML = `
    <td><select name="items[${itemIdx}][product_id]" class="form-select form-select-sm" required><option value="">انتخاب کالا...</option>${productOpts}</select></td>
    <td><select name="items[${itemIdx}][measurement_unit_id]" class="form-select form-select-sm"><option value="">—</option>${unitOpts}</select></td>
    <td><input type="number" name="items[${itemIdx}][quantity]" class="form-control form-control-sm" step="0.0001" min="0.0001" required></td>
    <td><input type="number" name="items[${itemIdx}][unit_price]" class="form-control form-control-sm" step="0.01" min="0" required></td>
    <td><input type="number" name="items[${itemIdx}][discount_percent]" class="form-control form-control-sm" step="0.01" min="0" max="100" value="0"></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger remove-item"><i class="fas fa-trash"></i></button></td>`;
  tbody.appendChild(tr);
  itemIdx++;
});

document.addEventListener('click', function(e) {
  if (e.target.closest('.remove-item')) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) e.target.closest('tr').remove();
  }
});

document.getElementById('returnType').addEventListener('change', function() {
  document.getElementById('salesInvoiceRef').classList.toggle('d-none', this.value !== 'sales');
  document.getElementById('purchaseInvoiceRef').classList.toggle('d-none', this.value !== 'purchase');
});
</script>
@endpush
@endsection
