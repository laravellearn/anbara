@extends('layouts.warehouse')
@section('title', 'سند انتقال جدید')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.transfers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">سند انتقال جدید</h4>
    <span class="badge bg-secondary-subtle text-secondary">{{ $number }}</span>
  </div>

  <form method="POST" action="{{ route('warehouse.transfers.store') }}" id="transferForm">
    @csrf
    <div class="row g-4">
      {{-- اطلاعات اصلی --}}
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">اطلاعات انتقال</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">انبار مبدأ <span class="text-danger">*</span></label>
                <select name="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror" required>
                  <option value="">انتخاب کنید</option>
                  @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" @selected(old('from_warehouse_id')==$wh->id)>{{ $wh->title }}</option>
                  @endforeach
                </select>
                @error('from_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">انبار مقصد <span class="text-danger">*</span></label>
                <select name="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror" required>
                  <option value="">انتخاب کنید</option>
                  @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}" @selected(old('to_warehouse_id')==$wh->id)>{{ $wh->title }}</option>
                  @endforeach
                </select>
                @error('to_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ انتقال <span class="text-danger">*</span></label>
                <input type="date" name="transfer_date" class="form-control @error('transfer_date') is-invalid @enderror"
                  value="{{ old('transfer_date', now()->format('Y-m-d')) }}" required>
                @error('transfer_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ تحویل پیش‌بینی‌شده</label>
                <input type="date" name="expected_arrival_date" class="form-control" value="{{ old('expected_arrival_date') }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">دلیل انتقال</label>
                <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="توضیح کوتاه...">
              </div>
              <div class="col-12">
                <label class="form-label">یادداشت</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- آیتم‌ها --}}
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">اقلام انتقالی</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
              <i class="fas fa-plus me-1"></i> افزودن ردیف
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0" id="itemsTable">
                <thead class="table-light">
                  <tr>
                    <th>کالا <span class="text-danger">*</span></th>
                    <th>واحد</th>
                    <th style="width:140px">مقدار <span class="text-danger">*</span></th>
                    <th style="width:150px">قیمت واحد</th>
                    <th style="width:40px"></th>
                  </tr>
                </thead>
                <tbody id="itemsBody">
                  <tr class="item-row">
                    <td>
                      <select name="items[0][product_id]" class="form-select form-select-sm" required>
                        <option value="">انتخاب کالا</option>
                        @foreach($products as $p)
                          <option value="{{ $p->id }}">{{ $p->title }} @if($p->sku)({{ $p->sku }})@endif</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select name="items[0][unit_id]" class="form-select form-select-sm">
                        <option value="">—</option>
                        @foreach($units as $u)
                          <option value="{{ $u->id }}">{{ $u->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm" min="0.0001" step="0.0001" required placeholder="۰"></td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm" min="0" step="1" placeholder="اختیاری"></td>
                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeItem(this)"><i class="fas fa-trash"></i></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- خلاصه --}}
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">خلاصه</h6>
            <div class="mb-2 d-flex justify-content-between">
              <span class="text-muted small">شماره سند</span>
              <span class="fw-medium">{{ $number }}</span>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-save me-1"></i> ذخیره سند انتقال
            </button>
            <a href="{{ route('warehouse.transfers.index') }}" class="btn btn-outline-secondary w-100 mt-2">انصراف</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
let rowCount = 1;
const products = @json($products->map(fn($p)=>['id'=>$p->id,'title'=>$p->title,'sku'=>$p->sku]));
const units    = @json($units->map(fn($u)=>['id'=>$u->id,'title'=>$u->title]));

function productOptions(idx) {
  return '<option value="">انتخاب کالا</option>' +
    products.map(p=>`<option value="${p.id}">${p.title}${p.sku?' ('+p.sku+')':''}</option>`).join('');
}
function unitOptions(idx) {
  return '<option value="">—</option>' + units.map(u=>`<option value="${u.id}">${u.title}</option>`).join('');
}

function addItem() {
  const i = rowCount++;
  const row = `<tr class="item-row">
    <td><select name="items[${i}][product_id]" class="form-select form-select-sm" required>${productOptions(i)}</select></td>
    <td><select name="items[${i}][unit_id]" class="form-select form-select-sm">${unitOptions(i)}</select></td>
    <td><input type="number" name="items[${i}][quantity]" class="form-control form-control-sm" min="0.0001" step="0.0001" required placeholder="۰"></td>
    <td><input type="number" name="items[${i}][unit_price]" class="form-control form-control-sm" min="0" step="1" placeholder="اختیاری"></td>
    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removeItem(this)"><i class="fas fa-trash"></i></button></td>
  </tr>`;
  document.getElementById('itemsBody').insertAdjacentHTML('beforeend', row);
}

function removeItem(btn) {
  const rows = document.querySelectorAll('.item-row');
  if (rows.length > 1) btn.closest('tr').remove();
}
</script>
@endpush
