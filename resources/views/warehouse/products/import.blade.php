@extends('layouts.master')
@section('title', 'ایمپورت کالا از Excel/CSV')

@section('content')
<div class="container-fluid py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="mb-1"><i class="bx bx-import me-2 text-success"></i>ایمپورت انبوه کالا</h4>
      <p class="text-muted mb-0">بارگذاری چندین کالا از فایل CSV یا Excel</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('warehouse.products.import.template') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-download me-1"></i>دانلود قالب CSV
      </a>
      <a href="{{ route('warehouse.products.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bx bx-list-ul me-1"></i>لیست کالاها
      </a>
    </div>
  </div>

  @if(session('import_errors') && count(session('import_errors')) > 0)
  <div class="alert alert-warning alert-dismissible fade show">
    <h6><i class="bx bx-error-circle me-1"></i>خطاهای ایمپورت:</h6>
    <ul class="mb-0 small">
      @foreach(session('import_errors') as $err)
      <li>{{ $err }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  @endif

  <div class="row">
    <div class="col-lg-7">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">آپلود فایل</h6></div>
        <div class="card-body">
          <form action="{{ route('warehouse.products.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">فایل CSV یا Excel <span class="text-danger">*</span></label>
              <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                     accept=".csv,.txt,.xls,.xlsx" required>
              @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
              <div class="form-text">حداکثر ۵ مگابایت — فرمت‌های مجاز: CSV، XLS، XLSX</div>
            </div>
            <button type="submit" class="btn btn-success">
              <i class="bx bx-upload me-1"></i>شروع ایمپورت
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card">
        <div class="card-header"><h6 class="mb-0">راهنمای ستون‌ها</h6></div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>نام ستون</th><th>اجباری</th><th>توضیح</th></tr></thead>
            <tbody>
              <tr><td class="fw-semibold">عنوان</td><td><i class="bx bx-check text-danger"></i></td><td>نام کالا</td></tr>
              <tr><td>کد_کالا</td><td>—</td><td>SKU منحصربه‌فرد</td></tr>
              <tr><td>بارکد</td><td>—</td><td>بارکد EAN/UPC</td></tr>
              <tr><td>مدل</td><td>—</td><td>شماره مدل</td></tr>
              <tr><td>دسته‌بندی</td><td>—</td><td>باید دقیقاً با نام دسته مطابق باشد</td></tr>
              <tr><td>واحد</td><td>—</td><td>باید دقیقاً با نام واحد مطابق باشد</td></tr>
              <tr><td>حداقل_موجودی</td><td>—</td><td>عدد — پیش‌فرض: ۰</td></tr>
              <tr><td>حداکثر_موجودی</td><td>—</td><td>عدد — اختیاری</td></tr>
              <tr><td>توضیحات</td><td>—</td><td>متن آزاد</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="alert alert-info mt-3 small">
        <i class="bx bx-info-circle me-1"></i>
        کالاهایی که کد کالای (SKU) تکراری دارند رد می‌شوند.<br>
        فایل باید با انکدینگ <strong>UTF-8</strong> ذخیره شده باشد.
      </div>
    </div>
  </div>

</div>
@endsection
