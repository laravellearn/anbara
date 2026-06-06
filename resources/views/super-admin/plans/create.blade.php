@extends('super-admin.layouts.master')
@section('title', 'ایجاد تعرفه جدید')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">تعرفه جدید</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('super-admin.plans.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">نام <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">قیمت ماهانه (تومان)</label>
                    <input type="number" name="monthly_price" class="form-control" value="{{ old('monthly_price', 0) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">قیمت سالانه (تومان)</label>
                    <input type="number" name="yearly_price" class="form-control" value="{{ old('yearly_price', 0) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">مدت اعتبار (روز، خالی = نامحدود)</label>
                    <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ترتیب نمایش</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active') ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">فعال</label>
                    </div>
                </div>
            </div>
            {{-- شما می‌توانید فیلدهای JSON مثل limits و features را با تکست‌ایریا یا فرم داینامیک اضافه کنید --}}
            <div class="mb-3">
                <label class="form-label">محدودیت‌ها (JSON)</label>
                <textarea name="limits" class="form-control" rows="4" placeholder='{"max_users": 10}' >{{ old('limits') }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">ویژگی‌ها (JSON)</label>
                <textarea name="features" class="form-control" rows="4" placeholder='["ویژگی ۱", "ویژگی ۲"]' >{{ old('features') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">ذخیره</button>
            <a href="{{ route('super-admin.plans.index') }}" class="btn btn-secondary">انصراف</a>
        </form>
    </div>
</div>
@endsection