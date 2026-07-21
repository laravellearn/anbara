@extends('layouts.app')
@section('title', isset($quotation->id) ? 'ویرایش پیش‌فاکتور' : 'پیش‌فاکتور جدید')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-11">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">{{ isset($quotation->id) ? 'ویرایش پیش‌فاکتور '.$quotation->quotation_number : 'پیش‌فاکتور جدید' }}</h5>
        </div>
        <form action="{{ isset($quotation->id) ? route('warehouse.quotations.update',$quotation) : route('warehouse.quotations.store') }}" method="POST" id="quotationForm">
          @csrf
          @if(isset($quotation->id)) @method('PUT') @endif
          <div class="card-body">
            {{-- اطلاعات اصلی --}}
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label class="form-label">مشتری</label>
                <select name="customer_id" class="form-select">
                  <option value="">انتخاب مشتری</option>
                  @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id',$quotation->customer_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">انبار</label>
                <select name="warehouse_id" class="form-select">
                  <option value="">انتخاب انبار</option>
                  @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ old('warehouse_id',$quotation->warehouse_id)==$w->id?'selected':'' }}>{{ $w->title }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                <input type="date" name="quotation_date" class="form-control" required value="{{ old('quotation_date',isset($quotation->quotation_date)?$quotation->quotation_date->format('Y-m-d'):date('Y-m-d')) }}">
              </div>
              <div class="col-md-2">
                <label class="form-label">اعتبار تا</label>
                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until',isset($quotation->valid_until)?$quotation->valid_until->format('Y-m-d'):'') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">توضیحات</label>
                <input type="text" name="description" class="form-control" value="{{ old('description',$quotation->description??'') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">شرایط و ضوابط</label>
                <input type="text" name="terms" class="form-control" value="{{ old('terms',$quotation->terms??'') }}">
              </div>
            </div>

            {{-- اقلام — همان ساختار فاکتور فروش --}}
            <hr class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">اقلام</h6>
              <button type="button" class="btn btn-sm btn-outline-primary" id="addRow">+ افزودن ردیف</button>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light">
                  <tr><th width="35%">کالا *</th><th>واحد</th><th>مقدار *</th><th>قیمت واحد *</th><th>تخفیف</th><th>جمع</th><th></th></tr>
                </thead>
                <tbody id="itemsBody">
                  @php $existingItems = old('items', isset($quotation->id)?$quotation->items->toArray():[['product_id'=>'','measurement_unit_id'=>'','quantity'=>'','unit_price'=>'','discount_amount'=>'']]); @endphp
                  @foreach($existingItems as $i=>$item)
                  <tr class="item-row">
                    <td>
                      <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm product-sel" required>
                        <option value="">انتخاب کالا</option>
                        @foreach($products as $p)
                          <option value="{{ $p->id }}" data-price="{{ $p->sale_price??0 }}" {{ ($item['product_id']??'')==$p->id?'selected':'' }}>{{ $p->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td>
                      <select name="items[{{ $i }}][measurement_unit_id]" class="form-select form-select-sm">
                        <option value="">—</option>
                        @foreach($units as $u)
                          <option value="{{ $u->id }}" {{ ($item['measurement_unit_id']??'')==$u->id?'selected':'' }}>{{ $u->title }}</option>
                        @endforeach
                      </select>
                    </td>
                    <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control form-control-sm qty" step="0.001" min="0.001" value="{{ $item['quantity']??'' }}" required></td>
                    <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control form-control-sm price" step="1" min="0" value="{{ $item['unit_price']??'' }}" required></td>
                    <td><input type="number" name="items[{{ $i }}][discount_amount]" class="form-control form-control-sm disc" step="1" min="0" value="{{ $item['discount_amount']??0 }}"></td>
                    <td><span class="row-total text-muted">—</span></td>
                    <td><button type="button" class="btn btn-xs btn-icon btn-outline-danger remove-row"><i data-feather="trash-2"></i></button></td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <div class="row justify-content-end mt-3">
              <div class="col-md-4">
                <table class="table table-sm">
                  <tr><td>جمع اقلام:</td><td class="text-end" id="sumSubtotal">—</td></tr>
                  <tr><td>تخفیف (%): <input type="number" name="discount_percent" class="form-control form-control-sm d-inline w-auto" style="width:60px" min="0" max="100" value="{{ old('discount_percent',$quotation->discount_percent??0) }}" id="discPct"></td><td class="text-end" id="sumDiscount">—</td></tr>
                  <tr><td>مالیات (%): <input type="number" name="tax_percent" class="form-control form-control-sm d-inline w-auto" style="width:60px" min="0" max="100" value="{{ old('tax_percent',$quotation->tax_percent??9) }}" id="taxPct"></td><td class="text-end" id="sumTax">—</td></tr>
                  <tr class="fw-bold"><td>جمع نهایی:</td><td class="text-end" id="sumTotal">—</td></tr>
                </table>
              </div>
            </div>
          </div>
          <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('warehouse.quotations.index') }}" class="btn btn-outline-secondary">انصراف</a>
            <button type="submit" class="btn btn-primary">{{ isset($quotation->id)?'ذخیره تغییرات':'ثبت پیش‌فاکتور' }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')
@include('warehouse.sales-invoices._scripts')
@endpush
