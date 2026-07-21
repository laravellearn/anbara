@extends('super-admin.layouts.master')
@section('title', 'ایجاد کاربر جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.users.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">ایجاد کاربر جدید</h4>
  </div>

  <div class="row">
    <div class="col-xl-7">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <form action="{{ route('super-admin.users.store') }}" method="POST">
            @csrf
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                  value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                  value="{{ old('email') }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">موبایل</label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile') }}">
              </div>
              <div class="col-md-6">
                <label class="form-label">سازمان <span class="text-danger">*</span></label>
                <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                  <option value="">انتخاب کنید</option>
                  @foreach($tenants as $t)
                  <option value="{{ $t->id }}" {{ old('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                  @endforeach
                </select>
                @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">تأیید رمز عبور <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
              </div>
              <div class="col-12">
                <div class="form-check form-switch">
                  <input type="hidden" name="is_active" value="0">
                  <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                  <label class="form-check-label" for="is_active">کاربر فعال باشد</label>
                </div>
              </div>
            </div>
            <hr class="my-4">
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>ذخیره</button>
              <a href="{{ route('super-admin.users.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
