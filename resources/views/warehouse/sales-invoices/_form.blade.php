{{-- فرم مشترک ایجاد و ویرایش فاکتور فروش --}}
<div class="row g-3">
  {{-- ردیف اول: مشتری، انبار، تاریخ --}}
  <div class="col-md-4">
    <label class="form-label">مشتری</label>
    <select name="customer_id" class="form-select">
      <option value="">انتخاب مشتری</option>
      @foreach($customers as $c)
        <option value="{{ $c->id }}" {{ old('customer_id', $salesInvoice->customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label">انبار</label>
    <select name="warehouse_id" class="form-select">
      <option value="">انتخاب انبار</option>
      @foreach($warehouses as $w)
        <option value="{{ $w->id }}" {{ old('warehouse_id', $salesInvoice->warehouse_id ?? '') == $w->id ? 'selected' : '' }}>{{ $w->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">تاریخ فاکتور <span class="text-danger">*</span></label>
    <input type="date" name="invoice_date" class="form-control @error('invoice_date') is-invalid @enderror"
           value="{{ old('invoice_date', isset($salesInvoice) ? $salesInvoice->invoice_date->format('Y-m-d') : date('Y-m-d')) }}" required>
    @error('invoice_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
  </div>
  <div class="col-md-2">
    <label class="form-label">سررسید پرداخت</label>
    <input type="date" name="due_date" class="form-control"
           value="{{ old('due_date', isset($salesInvoice) ? $salesInvoice->due_date?->format('Y-m-d') : '') }}">
  </div>

  {{-- ردیف دوم: سال مالی، مرکز هزینه، شماره مرجع --}}
  <div class="col-md-3">
    <label class="form-label">سال مالی</label>
    <select name="fiscal_year_id" class="form-select">
      <option value="">انتخاب</option>
      @foreach($fiscalYears as $fy)
        <option value="{{ $fy->id }}" {{ old('fiscal_year_id', $salesInvoice->fiscal_year_id ?? '') == $fy->id ? 'selected' : '' }}>{{ $fy->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">مرکز هزینه</label>
    <select name="cost_center_id" class="form-select">
      <option value="">انتخاب</option>
      @foreach($costCenters as $cc)
        <option value="{{ $cc->id }}" {{ old('cost_center_id', $salesInvoice->cost_center_id ?? '') == $cc->id ? 'selected' : '' }}>{{ $cc->title }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">شماره مرجع</label>
    <input type="text" name="reference_number" class="form-control" value="{{ old('reference_number', $salesInvoice->reference_number ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">توضیحات</label>
    <input type="text" name="description" class="form-control" value="{{ old('description', $salesInvoice->description ?? '') }}">
  </div>
</div>

{{-- ردیف کالاها --}}
<hr class="mt-4 mb-3">
<div class="d-flex justify-content-between align-items-center mb-2">
  <h6 class="mb-0">اقلام فاکتور</h6>
  <button type="button" class="btn btn-sm btn-outline-primary" id="addRow">+ افزودن ردیف</button>
</div>

<div class="table-responsive">
  <table class="table table-bordered align-middle" id="itemsTable">
    <thead class="table-light">
      <tr>
        <th width="35%">کالا <span class="text-danger">*</span></th>
        <th>واحد</th>
        <th>مقدار <span class="text-danger">*</span></th>
        <th>قیمت واحد <span class="text-danger">*</span></th>
        <th>تخفیف</th>
        <th>جمع</th>
        <th></th>
      </tr>
    </thead>
    <tbody id="itemsBody">
      @php $items = old('items', isset($salesInvoice) ? $salesInvoice->items->toArray() : [['product_id'=>'','measurement_unit_id'=>'','quantity'=>'','unit_price'=>'','discount_amount'=>'','description'=>'']]); @endphp
      @foreach($items as $i => $item)
      <tr class="item-row">
        <td>
          <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm product-sel" required>
            <option value="">انتخاب کالا</option>
            @foreach($products as $p)
              <option value="{{ $p->id }}" data-price="{{ $p->sale_price ?? 0 }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
            @endforeach
          </select>
        </td>
        <td>
          <select name="items[{{ $i }}][measurement_unit_id]" class="form-select form-select-sm">
            <option value="">—</option>
            @foreach($units as $u)
              <option value="{{ $u->id }}" {{ ($item['measurement_unit_id'] ?? '') == $u->id ? 'selected' : '' }}>{{ $u->title }}</option>
            @endforeach
          </select>
        </td>
        <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm qty" step="0.001" min="0.001" value="{{ $item['quantity'] ?? '' }}" required></td>
        <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price" step="1" min="0" value="{{ $item['unit_price'] ?? '' }}" required></td>
        <td><input type="number" name="items[{{ $i }}][discount_amount]" class="form-control form-control-sm disc" step="1" min="0" value="{{ $item['discount_amount'] ?? 0 }}"></td>
        <td><span class="row-total text-muted">—</span></td>
        <td><button type="button" class="btn btn-xs btn-icon btn-outline-danger remove-row"><i data-feather="trash-2"></i></button></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- جمع کل --}}
<div class="row justify-content-end mt-3">
  <div class="col-md-4">
    <table class="table table-sm">
      <tr><td>جمع اقلام:</td><td class="text-end" id="sumSubtotal">—</td></tr>
      <tr>
        <td>تخفیف کلی (%):
          <input type="number" name="discount_percent" class="form-control form-control-sm d-inline w-auto" style="width:60px" min="0" max="100" value="{{ old('discount_percent', $salesInvoice->discount_percent ?? 0) }}" id="discPct">
        </td>
        <td class="text-end" id="sumDiscount">—</td>
      </tr>
      <tr>
        <td>مالیات (%):
          <input type="number" name="tax_percent" class="form-control form-control-sm d-inline w-auto" style="width:60px" min="0" max="100" value="{{ old('tax_percent', $salesInvoice->tax_percent ?? 9) }}" id="taxPct">
        </td>
        <td class="text-end" id="sumTax">—</td>
      </tr>
      <tr class="fw-bold"><td>جمع نهایی:</td><td class="text-end" id="sumTotal">—</td></tr>
    </table>
  </div>
</div>
