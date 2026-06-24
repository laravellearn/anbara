@extends('layouts.master')

@section('title', 'پروفایل')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        @include('errors.error')

        <div class="col-md-6">
            <div class="card mb-4 shadow-none border">
                <div class="card-header">
                    <h5 class="card-title mb-0">اطلاعات حساب</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')


                        {{-- آواتار --}}
                        <div class="mb-3 text-center">
                            <div class="mb-2">
                                <img id="avatar_preview"
                                    src="{{ asset($user->avatar ?? '/img/avatars/avatar.png') }}?v={{ time() }}"
                                    alt="avatar" class="rounded-circle" width="100" height="100">
                            </div>
                            <label class="form-label">تصویر پروفایل</label>
                            <input type="file" id="avatar_input" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            <input type="hidden" name="avatar_base64" id="avatar_base64">
                            @error('avatar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نام <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">موبایل <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $user->mobile }}" readonly dir="ltr">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ایمیل</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email) }}" dir="ltr">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">کد ملی</label>
                            <input type="text" name="national_code" class="form-control @error('national_code') is-invalid @enderror"
                                value="{{ old('national_code', $user->national_code) }}">
                            @error('national_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-primary">ذخیره تغییرات</button>

                        <script>
                            document.getElementById('avatar_input').addEventListener('change', function(e) {
                                const file = e.target.files[0];
                                if (!file) return;

                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    // پیش‌نمایش
                                    document.getElementById('avatar_preview').src = ev.target.result;
                                    // ذخیره رشتهٔ Base64 در فیلد مخفی
                                    document.getElementById('avatar_base64').value = ev.target.result;
                                };
                                reader.readAsDataURL(file);
                            });
                        </script>
                    </form>
                </div>
            </div>
        </div>

        {{-- بخش تغییر رمز عبور --}}
        <div class="col-md-6">
            <div class="card mb-4 shadow-none border">
                <div class="card-header">
                    <h5 class="card-title mb-0">تغییر رمز عبور</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">رمز عبور فعلی</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رمز عبور جدید</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">تکرار رمز جدید</label>
                            <input type="password" name="password_confirmation"
                                class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-danger">تغییر رمز</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection