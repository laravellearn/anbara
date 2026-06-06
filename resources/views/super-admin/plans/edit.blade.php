@extends('super-admin.layouts.master')
@section('title', 'ویرایش تعرفه - ' . $plan->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="card-title">ویرایش تعرفه</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('super-admin.plans.update', $plan) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">نام</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $plan->slug) }}" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">قیمت ماهانه</label>
                    <input type="number" name="monthly_price" class="form-control" value="{{ old('monthly_price', $plan->monthly_price) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">قیمت سالانه</label>
                    <input type="number" name="yearly_price" class="form-control" value="{{ old('yearly_price', $plan->yearly_price) }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">مدت اعتبار (روز)</label>
                    <input type="number" name="duration_days" class="form-control" value="{{ old('duration_days', $plan->duration_days) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ترتیب</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $plan->sort_order) }}">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-center">
                    <div class="form-check mt-4">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="form-check-input" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label">فعال</label>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">محدودیت‌ها (JSON)</label>
                <textarea name="limits" class="form-control" rows="4">{{ old('limits', $plan->limits) }}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">ویژگی‌ها (JSON)</label>
                <textarea name="features" class="form-control" rows="4">{{ old('features', $plan->features) }}</textarea>
            </div>
            <button type="submit" class="btn btn-warning">بروزرسانی</button>
            <a href="{{ route('super-admin.plans.index') }}" class="btn btn-secondary">بازگشت</a>
        </form>
    </div>
</div>
@endsection