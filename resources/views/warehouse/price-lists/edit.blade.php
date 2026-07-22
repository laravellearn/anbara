@extends('layouts.warehouse')
@section('title', 'ویرایش لیست قیمت')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.price-lists.show', $priceList) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">ویرایش — {{ $priceList->name }}</h4>
  </div>

  <form method="POST" action="{{ route('warehouse.price-lists.update', $priceList) }}" id="editPriceForm">
    @csrf @method('PUT')
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">اطلاعات پایه</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $priceList->name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">نوع</label>
                <select name="type" class="form-select">
                  @foreach(['retail'=>'خرده‌فروشی','wholesale'=>'عمده‌فروشی','vip'=>'VIP','special'=>'ویژه'] as $v=>$l)
                    <option value="{{ $v }}" @selected(old('type',$priceList->type)===$v)>{{ $l }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">از تاریخ</label>
                <input type="date" name="valid_from" class="form-control" value="{{ old('valid_from', $priceList->valid_from?->format('Y-m-d')) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">تا تاریخ</label>
                <input type="date" name="valid_to" class="form-control" value="{{ old('valid_to', $priceList->valid_to?->format('Y-m-d')) }}">
              </div>
              <div class="col-md-4 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive" @checked(old('is_active', $priceList->is_active))>
                  <label class="form-check-label" for="editActive">فعال</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <span class="fw-semibold">اقلام قیمت</span>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEditRow()">
              <i class="fas fa-plus me-1"></i> افزودن
            </button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>کالا</th>
                    <th style="width:130px">قیمت واحد</th>
                    <th style="width:110px">حداقل مقدار</th>
                    <th style="width:110px">تخفیف (%)</th>
                    <th style="width:120px" class="text-end">قیمت نهایی</th>
                    <th style="width:40px"></th>
                  </tr>
                </thead>
                <tbody id="editPriceBody">
                  @foreach($priceList->items as $i => $item)
                  <tr class="price-row">
                    <td>
                      <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm" required>
                        @foreach($products as $p)
                          <option value="{{ $p->id }}" @selected($p->id === $item->product_id)>{{ $p->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price-input"
                      value="{{ $item->unit_price }}" min="0" step="1" required oninput="calcFinalE(this)"></td>
                    <td><input type="number" name="items[{{ $i }}][min_quantity]" class="form-control form-control-sm"
                      value="{{ $item->min_quantity }}" min="1" step="1"></td>
                    <td><input type="number" name="items[{{ $i }}][discount_percent]" class="form-control form-control-sm disc-input"
                      value="{{ $item->discount_percent }}" min="0" max="100" step="0.01" oninput="calcFinalE(this)"></td>
                    <td class="text-end text-success fw-medium final-price">{{ number_format($item->final_price) }} ﷼</td>
                    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-save me-1"></i> ذخیره تغییرات
            </button>
            <a href="{{ route('warehouse.price-lists.show', $priceList) }}" class="btn btn-outline-secondary w-100 mt-2">انصراف</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
let editRowCount = {{ $priceList->items->count() }};
const editProducts = @json($products->map(fn($p)=>['id'=>$p->id,'title'=>$p->title]));

function addEditRow() {
  const i = editRowCount++;
  const opts = editProducts.map(p=>`<option value="${p.id}">${p.title}</option>`).join('');
  const row = `<tr class="price-row">
    <td><select name="items[${i}][product_id]" class="form-select form-select-sm" required><option value="">انتخاب</option>${opts}</select></td>
    <td><input type="number" name="items[${i}][unit_price]" class="form-control form-control-sm price-input" min="0" step="1" required oninput="calcFinalE(this)"></td>
    <td><input type="number" name="items[${i}][min_quantity]" class="form-control form-control-sm" min="1" step="1" value="1"></td>
    <td><input type="number" name="items[${i}][discount_percent]" class="form-control form-control-sm disc-input" min="0" max="100" step="0.01" value="0" oninput="calcFinalE(this)"></td>
    <td class="text-end text-success fw-medium final-price">—</td>
    <td><button type="button" class="btn btn-sm btn-icon btn-outline-danger" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
  </tr>`;
  document.getElementById('editPriceBody').insertAdjacentHTML('beforeend', row);
}

function calcFinalE(el) {
  const row   = el.closest('tr');
  const price = parseFloat(row.querySelector('.price-input').value) || 0;
  const disc  = parseFloat(row.querySelector('.disc-input').value)  || 0;
  const final = price * (1 - disc / 100);
  row.querySelector('.final-price').textContent = final > 0 ? new Intl.NumberFormat('fa-IR').format(Math.round(final)) + ' ﷼' : '—';
}
</script>
@endpush
