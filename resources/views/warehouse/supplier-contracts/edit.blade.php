@extends('layouts.warehouse')
@section('title', 'ویرایش قرارداد')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.supplier-contracts.show', $supplierContract) }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">ویرایش قرارداد — {{ $supplierContract->contract_number }}</h4>
  </div>

  <form method="POST" action="{{ route('warehouse.supplier-contracts.update', $supplierContract) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-transparent fw-semibold">اطلاعات قرارداد</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">تأمین‌کننده</label>
                <input type="text" class="form-control" value="{{ $supplierContract->supplier?->name }}" disabled>
              </div>
              <div class="col-md-6">
                <label class="form-label">عنوان <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                  value="{{ old('title', $supplierContract->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control"
                  value="{{ old('start_date', $supplierContract->start_date->format('Y-m-d')) }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                  value="{{ old('end_date', $supplierContract->end_date->format('Y-m-d')) }}" required>
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">وضعیت</label>
                <select name="status" class="form-select">
                  @foreach(['draft'=>'پیش‌نویس','active'=>'فعال','expired'=>'منقضی','terminated'=>'فسخ‌شده'] as $v=>$l)
                    <option value="{{ $v }}" @selected(old('status',$supplierContract->status)===$v)>{{ $l }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">سقف اعتبار</label>
                <input type="number" name="credit_limit" class="form-control"
                  value="{{ old('credit_limit', $supplierContract->credit_limit) }}" min="0" step="1000">
              </div>
              <div class="col-md-4">
                <label class="form-label">شرایط پرداخت (روز)</label>
                <input type="number" name="payment_terms_days" class="form-control"
                  value="{{ old('payment_terms_days', $supplierContract->payment_terms_days) }}" min="0">
              </div>
              <div class="col-md-4">
                <label class="form-label">تخفیف (%)</label>
                <input type="number" name="discount_percent" class="form-control"
                  value="{{ old('discount_percent', $supplierContract->discount_percent) }}" min="0" max="100" step="0.01">
              </div>
              <div class="col-12">
                <label class="form-label">شرایط و ضوابط</label>
                <textarea name="terms_and_conditions" class="form-control" rows="4">{{ old('terms_and_conditions', $supplierContract->terms_and_conditions) }}</textarea>
              </div>
              <div class="col-12">
                <label class="form-label">یادداشت</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $supplierContract->notes) }}</textarea>
              </div>
              <div class="col-12">
                <label class="form-label">فایل جدید (اختیاری)</label>
                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx">
                @if($supplierContract->file_path)
                  <small class="text-muted">فایل فعلی موجود است. آپلود جدید جایگزین می‌شود.</small>
                @endif
              </div>
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
            <a href="{{ route('warehouse.supplier-contracts.show', $supplierContract) }}" class="btn btn-outline-secondary w-100 mt-2">انصراف</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
