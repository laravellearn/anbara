@extends('layouts.master')
@section('title', 'پروفایل')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5>اطلاعات حساب</h5></div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">نام</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">موبایل</label>
                            <input type="text" name="mobile" class="form-control" value="{{ old('mobile', auth()->user()->mobile) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}">
                        </div>
                        <button type="submit" class="btn btn-primary">ذخیره</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header"><h5>تغییر رمز عبور</h5></div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">رمز عبور فعلی</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رمز عبور جدید</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تکرار رمز جدید</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-danger">تغییر رمز</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection