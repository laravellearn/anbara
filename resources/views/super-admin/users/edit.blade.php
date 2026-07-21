@extends('super-admin.layouts.master')
@section('title', 'ویرایش کاربر - ' . $user->name)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">ویرایش: {{ $user->name }}</h4>
  </div>

  <div class="row">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form action="{{ route('super-admin.users.update', $user) }}" method="POST">
            @csrf @method('PUT')
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">موبایل</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">سازمان <span class="text-danger">*</span></label>
                <select name="tenant_id" class="form-select" required>
                  @foreach($tenants as $t)
                  <option value="{{ $t->id }}" {{ old('tenant_id', $user->tenant_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">رمز عبور جدید <span class="text-muted small">(اختیاری)</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">تأیید رمز عبور</label>
                <input type="password" name="password_confirmation" class="form-control">
              </div>
              <div class="col-12">
                <div class="form-check form-switch">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                    {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">کاربر فعال باشد</label>
                </div>
              </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning"><i class="bx bx-save me-1"></i>به‌روزرسانی</button>
              <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
