@extends('super-admin.layouts.master')
@section('title', 'ویرایش سازمان - ' . $tenant->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">ویرایش: {{ $tenant->name }}</h4>
  </div>

  <div class="row">
    <div class="col-xl-8">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST">
            @csrf @method('PUT')
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name', $tenant->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                  value="{{ old('slug', $tenant->slug) }}" required>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">عنوان نمایشی</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $tenant->title) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">دامنه</label>
                <input type="text" name="domain" class="form-control" value="{{ old('domain', $tenant->domain) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}">
              </div>
              <div class="col-md-12">
                <label class="form-label">آدرس</label>
                <input type="text" name="address" class="form-control" value="{{ old('address', $tenant->address) }}">
              </div>
            </div>

            <hr class="my-4">
            <div class="form-check form-switch mb-3">
              <input type="hidden" name="is_active" value="0">
              <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">سازمان فعال باشد</label>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning"><i class="bx bx-save me-1"></i>به‌روزرسانی</button>
              <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
