@extends('layouts.warehouse')
@section('title', 'لیست قیمت جدید')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.price-lists.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">لیست قیمت جدید</h4>
  </div>

  <form method="POST" action="{{ route('warehouse.price-lists.store') }}" id="priceListForm">
    @csrf
    <div class="row g-4">
      <div class="col-lg-8">
        {{-- اطلاعات پایه --}}
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">اطلاعات لیست قیمت</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام لیست <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}" required placeholder="مثلاً: لیست قیمت عمده — تابستان ۱۴۰۳">
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">نوع <span class="text-danger">*</span></label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                  @foreach(['retail'=>'خرده‌فروشی','wholesale'=>'عمده‌فروشی','vip'=>'مشتریان VIP','special'=>'قیمت ویژه'] as $v=>$l)
                    <option value="{{ $v }}" @selected(old('type')===$v)>{{ $l }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">اعتبار از</label>
                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">اعتبار تا</label>
                <input type="date" name="valid_to" class="form-control @error('valid_to') is-invalid @enderror" value="{{ old('valid_to') }}">
                @error('valid_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" @checked(old('is_active', true))>
                  <label class="form-check-label" for="isActive">فعال</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">توضیح</label>
                <textarea name="description" class="form-control" rows="2" placeholder="توضیح کوتاه درباره این لیست قیمت...">{{ old('description') }}</textarea>
              </div>
            </div>
          </div>
        </div>

        {{-- آیتم‌های قیمت --}}
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">اقلام و قیمت‌ها</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPriceRow()">
              <i class="fas fa-plus me-1"></i> افزودن کالا
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0" id="priceTable">
                <thead class="table-light">
                  <tr>
                    <th>کالا <span class="text-danger">*</span></th>
                    <th style="width:130px">قیمت واحد <span class="text-danger">*</span></th>
                    <th style="width:110px">حداقل مقدار</th>
                    <th style="width:110px">تخفیف (%)</th>
                    <th style="width:120px" class="text-end text-success fw-semibold">قیمت نهایی</th>
                    <th style="width:40px"></th>
                  </tr>
                </thead>
                <tbody id="priceBody">
                  <tr class="price-row">
                    <td>
                      <select name="items[0][product_id]" class="form-select form-select-sm" required>
                        <option value="">انتخاب کالا</option>
                        @foreach($products as $p)
                          <option value="{{ $p->id }}">{{ $p->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm price-input" min="0" step="1" required oninput="calcFinal(this)"></td>
                    <td><input type="number" name="items[0][min_quantity]" class="form-control form-control-sm" min="1" step="1" value="1"></td>
                    <td><input type="number" name="items[0][discount_percent]" class="form-control form-control-sm disc-input" min="0" max="100" step="0.01" value="0" oninput="calcFinal(this)"></td>
                    <td class="text-end text-success fw-medium final-price">—</td>
                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removePriceRow(this)"><i class="fas fa-trash"></i></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">تنظیمات</h6>
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-save me-1"></i> ذخیره لیست قیمت
            </button>
            <a href="{{ route('warehouse.price-lists.index') }}" class="btn btn-outline-secondary w-100 mt-2">انصراف</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
let priceRowCount = 1;
const allProducts = @json($products->map(fn($p)=>['id'=>$p->id,'title'=>$p->title]));

function productOpts() {
  return '<option value="">انتخاب کالا</option>' + allProducts.map(p=>`<option value="${p.id}">${p.title}</option>`).join('');
}

function addPriceRow() {
  const i = priceRowCount++;
  const row = `<tr class="price-row">
    <td><select name="items[${i}][product_id]" class="form-select form-select-sm" required>${productOpts()}</select></td>
    <td><input type="number" name="items[${i}][unit_price]" class="form-control form-control-sm price-input" min="0" step="1" required oninput="calcFinal(this)"></td>
    <td><input type="number" name="items[${i}][min_quantity]" class="form-control form-control-sm" min="1" step="1" value="1"></td>
    <td><input type="number" name="items[${i}][discount_percent]" class="form-control form-control-sm disc-input" min="0" max="100" step="0.01" value="0" oninput="calcFinal(this)"></td>
    <td class="text-end text-success fw-medium final-price">—</td>
    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="removePriceRow(this)"><i class="fas fa-trash"></i></button></td>
  </tr>`;
  document.getElementById('priceBody').insertAdjacentHTML('beforeend', row);
}

function removePriceRow(btn) {
  if (document.querySelectorAll('.price-row').length > 1) btn.closest('tr').remove();
}

function calcFinal(el) {
  const row   = el.closest('tr');
  const price = parseFloat(row.querySelector('.price-input').value) || 0;
  const disc  = parseFloat(row.querySelector('.disc-input').value)  || 0;
  const final = price * (1 - disc / 100);
  row.querySelector('.final-price').textContent = final > 0 ? new Intl.NumberFormat('fa-IR').format(Math.round(final)) + ' ﷼' : '—';
}
</script>
@endpush
