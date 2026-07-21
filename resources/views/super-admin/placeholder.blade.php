@extends('super-admin.layouts.master')
@section('title', 'به‌زودی')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-12">
      <div class="card border-0 shadow-sm text-center py-5">
        <div class="card-body">
          <i class="bx bx-time-five fs-1 text-muted mb-3 d-block"></i>
          <h4 class="fw-bold text-muted">این بخش به‌زودی فعال می‌شود</h4>
          <p class="text-muted">این قابلیت در نسخه‌های آتی سیستم اضافه خواهد شد.</p>
          <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-primary mt-2">
            <i class="bx bx-arrow-back me-1"></i> بازگشت به داشبورد
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
