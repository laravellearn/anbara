@extends('layouts.master')
@section('title', 'تنظیمات شرکت')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1"><i class="bx bx-buildings me-2 text-primary"></i>تنظیمات شرکت</h4>
      <p class="text-muted mb-0">اطلاعات سربرگ چاپی، لوگو و مشخصات سازمان</p>
    </div>
  </div>

  <form action="{{ route('warehouse.settings.company.update') }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-4">
      {{-- ─── اطلاعات اصلی ─── --}}
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header"><h6 class="mb-0">اطلاعات سازمان</h6></div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold">نام شرکت</label>
                <input type="text" name="company_name" class="form-control"
                       value="{{ $settings['company_name']?->value ?? '' }}" placeholder="نام شرکت یا برند">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">شماره ثبت</label>
                <input type="text" name="company_reg_no" class="form-control"
                       value="{{ $settings['company_reg_no']?->value ?? '' }}" placeholder="شماره ثبت شرکت">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">شناسه مالیاتی</label>
                <input type="text" name="company_tax_no" class="form-control"
                       value="{{ $settings['company_tax_no']?->value ?? '' }}" placeholder="کد اقتصادی یا شناسه ملی">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">تلفن</label>
                <input type="text" name="company_phone" class="form-control"
                       value="{{ $settings['company_phone']?->value ?? '' }}" placeholder="۰۲۱-XXXXXXXX">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">ایمیل</label>
                <input type="email" name="company_email" class="form-control"
                       value="{{ $settings['company_email']?->value ?? '' }}" placeholder="info@company.ir">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold">وب‌سایت</label>
                <input type="text" name="company_website" class="form-control"
                       value="{{ $settings['company_website']?->value ?? '' }}" placeholder="www.company.ir">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">آدرس</label>
                <textarea name="company_address" class="form-control" rows="2"
                          placeholder="آدرس کامل شرکت">{{ $settings['company_address']?->value ?? '' }}</textarea>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold">متن پاورقی چاپ</label>
                <textarea name="print_footer" class="form-control" rows="2"
                          placeholder="متنی که در پایین اسناد چاپی نمایش داده می‌شود">{{ $settings['print_footer']?->value ?? '' }}</textarea>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ─── لوگو ─── --}}
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header"><h6 class="mb-0">لوگوی شرکت</h6></div>
          <div class="card-body text-center">
            @php $logoPath = $settings['company_logo']?->value ?? null; @endphp
            @if($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
              <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath) }}"
                   alt="لوگو" class="img-fluid mb-3 rounded border" style="max-height:120px">
            @else
              <div class="border rounded d-flex align-items-center justify-content-center mb-3"
                   style="height:120px;background:#f8f9fa">
                <i class="bx bx-image fs-1 text-muted"></i>
              </div>
            @endif
            <div>
              <input type="file" name="logo" class="form-control form-control-sm @error('logo') is-invalid @enderror"
                     accept=".jpg,.jpeg,.png,.gif,.svg">
              @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">PNG یا JPG — حداکثر ۱ مگابایت</div>
            </div>
          </div>
        </div>

        <div class="alert alert-info mt-3 small">
          <i class="bx bx-info-circle me-1"></i>
          این اطلاعات روی <strong>همه اسناد چاپی</strong> (سفارش خرید، سند انبار) در سربرگ نمایش داده می‌شود.
        </div>
      </div>
    </div>

    <div class="mt-3">
      <button type="submit" class="btn btn-primary">
        <i class="bx bx-save me-1"></i>ذخیره تنظیمات
      </button>
    </div>
  </form>

</div>
@endsection
