@extends('super-admin.layouts.master')
@section('title', 'ویرایش سازمان - ' . $tenant->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">ویرایش اطلاعات</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">نام سازمان</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $tenant->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">slug</label>
                <input type="text" name="slug" class="form-control" value="{{ old('slug', $tenant->slug) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ایمیل</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $tenant->email) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $tenant->phone) }}">
            </div>
            <div class="mb-3 form-check">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">فعال</label>
            </div>
            <button type="submit" class="btn btn-warning">بروزرسانی</button>
            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>
@endsection