@extends('super-admin.layouts.master')
@section('title', 'ایجاد سازمان جدید')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">سازمان جدید</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">نام سازمان <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">slug <span class="text-danger">*</span></label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="مثال: company-name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>
            <div class="mb-3 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active') ? 'checked' : '' }} id="is_active">
                <label class="form-check-label" for="is_active">فعال باشد؟</label>
            </div>
            <button type="submit" class="btn btn-primary">ذخیره</button>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">انصراف</a>
        </form>
    </div>
</div>
@endsection