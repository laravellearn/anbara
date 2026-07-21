@extends('super-admin.layouts.master')
@section('title', 'ایجاد سازمان جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">ایجاد سازمان جدید</h4>
  </div>

  <div class="row">
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-buildings text-primary me-2"></i>اطلاعات سازمان</h6>
        </div>
        <div class="card-body">
          <form action="{{ route('super-admin.tenants.store') }}" method="POST">
            @csrf
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">شناسه (slug) <span class="text-danger">*</span></label>
                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
                  value="{{ old('slug') }}" placeholder="مثال: my-company" required>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">عنوان (نمایشی)</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">دامنه اختصاصی</label>
                <input type="text" name="domain" class="form-control" value="{{ old('domain') }}" placeholder="example.com">
              </div>
              <div class="col-md-6">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
              </div>
              <div class="col-md-12">
                <label class="form-label">آدرس</label>
                <input type="text" name="address" class="form-control" value="{{ old('address') }}">
              </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-3"><i class="bx bx-credit-card text-warning me-2"></i>تخصیص اولیه پلن</h6>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">پلن</label>
                <select name="plan_id" class="form-select">
                  <option value="">بدون پلن</option>
                  @foreach($plans as $plan)
                  <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                    {{ $plan->name }} — {{ number_format($plan->monthly_price) }} تومان/ماه
                  </option>
                  @endforeach
                </select>
              </div>
            </div>

            <hr class="my-4">
            <div class="form-check form-switch mb-3">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', '1') ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">سازمان فعال باشد</label>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>ذخیره</button>
              <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  document.querySelector('[name=name]').addEventListener('input', function() {
    const slug = document.getElementById('slug');
    if (!slug.dataset.modified) {
      slug.value = this.value.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-]/g, '');
    }
  });
  document.getElementById('slug').addEventListener('input', function() {
    this.dataset.modified = '1';
  });
</script>
@endpush
@endsection
