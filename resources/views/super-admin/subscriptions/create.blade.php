@extends('super-admin.layouts.master')
@section('title', 'تخصیص اشتراک جدید')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-sm btn-icon btn-outline-secondary">
      <i class="bx bx-arrow-back"></i>
    </a>
    <h4 class="fw-bold mb-0">تخصیص اشتراک جدید</h4>
  </div>

  <div class="row">
    <div class="col-xl-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header py-3">
          <h6 class="mb-0"><i class="bx bx-credit-card text-primary me-2"></i>اطلاعات اشتراک</h6>
        </div>
        <div class="card-body">
          <form action="{{ route('super-admin.subscriptions.store') }}" method="POST">
            @csrf
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
              </div>
            @endif
            <div class="mb-3">
              <label class="form-label">سازمان <span class="text-danger">*</span></label>
              <select name="tenant_id" class="form-select @error('tenant_id') is-invalid @enderror" required>
                <option value="">انتخاب کنید</option>
                @foreach($tenants as $t)
                <option value="{{ $t->id }}" {{ old('tenant_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                @endforeach
              </select>
              @error('tenant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">پلن <span class="text-danger">*</span></label>
              <select name="plan_id" class="form-select @error('plan_id') is-invalid @enderror" required>
                <option value="">انتخاب کنید</option>
                @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                  {{ $plan->name }} — {{ number_format($plan->monthly_price) }} تومان/ماه
                  {{ $plan->duration_days ? '(' . $plan->duration_days . ' روز)' : '(نامحدود)' }}
                </option>
                @endforeach
              </select>
              @error('plan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
              <input type="date" name="starts_at" class="form-control" value="{{ old('starts_at', date('Y-m-d')) }}" required>
            </div>
            <div class="form-check mb-3">
              <input type="checkbox" name="cancel_old" value="1" class="form-check-input" id="cancel_old" checked>
              <label class="form-check-label" for="cancel_old">لغو اشتراک فعلی سازمان</label>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>ثبت اشتراک</button>
              <a href="{{ route('super-admin.subscriptions.index') }}" class="btn btn-outline-secondary">انصراف</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
