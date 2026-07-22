@extends('layouts.warehouse')
@section('title', 'قرارداد جدید')

@section('content')
<div class="container-fluid">
  <div class="d-flex align-items-center mb-4 gap-3">
    <a href="{{ route('warehouse.supplier-contracts.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-right"></i></a>
    <h4 class="mb-0 fw-bold">قرارداد جدید</h4>
    <span class="badge bg-secondary-subtle text-secondary">{{ $number }}</span>
  </div>

  <form method="POST" action="{{ route('warehouse.supplier-contracts.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-transparent fw-semibold">اطلاعات قرارداد</div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">تأمین‌کننده <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                  <option value="">انتخاب کنید</option>
                  @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id')==$s->id)>{{ $s->name }}</option>
                  @endforeach
                </select>
                @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">عنوان قرارداد <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                  value="{{ old('title') }}" required placeholder="مثلاً: قرارداد سالانه تأمین مواد اولیه">
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                  value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                  value="{{ old('end_date') }}" required>
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-4">
                <label class="form-label">شرایط پرداخت (روز)</label>
                <input type="number" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days', 30) }}" min="0">
              </div>
              <div class="col-md-4">
                <label class="form-label">سقف اعتبار (ریال)</label>
                <input type="number" name="credit_limit" class="form-control" value="{{ old('credit_limit', 0) }}" min="0" step="1000">
              </div>
              <div class="col-md-4">
                <label class="form-label">درصد تخفیف قراردادی</label>
                <div class="input-group">
                  <input type="number" name="discount_percent" class="form-control" value="{{ old('discount_percent', 0) }}" min="0" max="100" step="0.01">
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">فایل قرارداد</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept=".pdf,.doc,.docx">
                <small class="text-muted">PDF یا Word، حداکثر ۵ مگابایت</small>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">شرایط و ضوابط</label>
                <textarea name="terms_and_conditions" class="form-control" rows="4" placeholder="متن کامل شرایط قرارداد...">{{ old('terms_and_conditions') }}</textarea>
              </div>
              <div class="col-12">
                <label class="form-label">یادداشت داخلی</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <h6 class="fw-semibold mb-3">خلاصه</h6>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted small">شماره قرارداد</span>
              <span class="fw-medium">{{ $number }}</span>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary w-100">
              <i class="fas fa-save me-1"></i> ثبت قرارداد
            </button>
            <a href="{{ route('warehouse.supplier-contracts.index') }}" class="btn btn-outline-secondary w-100 mt-2">انصراف</a>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
@endsection
