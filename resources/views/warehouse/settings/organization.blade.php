@extends('layouts.master')
@section('title', 'اطلاعات سازمان')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-building me-2 text-primary"></i> اطلاعات سازمان</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- تب‌های تنظیمات --}}
    @include('warehouse.settings._tabs', ['active' => 'organization'])

    <div class="card shadow-none border">
        <div class="card-header border-bottom"><h6 class="card-title mb-0">مشخصات سازمان / شرکت</h6></div>
        <form method="POST" action="{{ route('warehouse.settings.organization.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="row g-3">
                    {{-- لوگوی سازمان --}}
                    <div class="col-12">
                        <label class="form-label fw-medium">لوگوی سازمان</label>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            @if(!empty($settings['org_logo']))
                                <img src="{{ asset('storage/' . $settings['org_logo']) }}" alt="لوگو" style="max-height:80px;max-width:200px;border:1px solid #ddd;border-radius:6px;padding:4px;background:#fff">
                                <form method="POST" action="{{ route('warehouse.settings.organization.delete-logo') }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('لوگو حذف شود؟')"><i class="bx bx-trash me-1"></i> حذف لوگو</button>
                                </form>
                            @else
                                <div class="text-muted small"><i class="bx bx-image me-1"></i> لوگویی آپلود نشده</div>
                            @endif
                            <div>
                                <input type="file" name="org_logo" class="form-control @error('org_logo') is-invalid @enderror" accept="image/*" style="max-width:300px">
                                <div class="form-text">فرمت‌های مجاز: PNG, JPG, SVG — حداکثر ۲ مگابایت</div>
                                @error('org_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- رنگ اصلی برند --}}
                    <div class="col-md-4">
                        <label class="form-label fw-medium">رنگ اصلی برند</label>
                        <div class="input-group">
                            <input type="color" name="org_brand_color" class="form-control form-control-color"
                                value="{{ old('org_brand_color', $settings['org_brand_color'] ?? '#3B82F6') }}"
                                title="رنگ اصلی سازمان را انتخاب کنید" style="max-width:60px">
                            <input type="text" id="brandColorText" class="form-control"
                                value="{{ old('org_brand_color', $settings['org_brand_color'] ?? '#3B82F6') }}"
                                placeholder="#3B82F6" maxlength="7" readonly>
                        </div>
                        <div class="form-text">این رنگ در سربرگ چاپ فاکتورها استفاده می‌شود.</div>
                    </div>

                    {{-- نام سازمان --}}
                    <div class="col-md-8">
                        <label class="form-label fw-medium">نام سازمان <span class="text-danger">*</span></label>
                        <input type="text" name="org_name" class="form-control @error('org_name') is-invalid @enderror"
                            value="{{ old('org_name', $settings['org_name'] ?? '') }}" required>
                        @error('org_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">آدرس ایمیل</label>
                        <input type="email" name="org_email" class="form-control"
                            value="{{ old('org_email', $settings['org_email'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">شماره تلفن</label>
                        <input type="text" name="org_phone" class="form-control"
                            value="{{ old('org_phone', $settings['org_phone'] ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">وب‌سایت</label>
                        <input type="url" name="org_website" class="form-control" placeholder="https://"
                            value="{{ old('org_website', $settings['org_website'] ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">کد ملی</label>
                        <input type="text" name="org_national_code" class="form-control"
                            value="{{ old('org_national_code', $settings['org_national_code'] ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">کد اقتصادی</label>
                        <input type="text" name="org_economic_code" class="form-control"
                            value="{{ old('org_economic_code', $settings['org_economic_code'] ?? '') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">شماره ثبت</label>
                        <input type="text" name="org_registration_number" class="form-control"
                            value="{{ old('org_registration_number', $settings['org_registration_number'] ?? '') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">آدرس</label>
                        <textarea name="org_address" class="form-control" rows="2">{{ old('org_address', $settings['org_address'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>

@push('scripts')
<script>
document.querySelector('input[name="org_brand_color"]').addEventListener('input', function () {
    document.getElementById('brandColorText').value = this.value;
});
</script>
@endpush
</div>
@endsection
