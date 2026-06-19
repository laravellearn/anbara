@extends('layouts.master')

@section('title', 'وضعیت لایسنس')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- نمایش خطاها در صورت وجود --}}
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="bx bx-error-circle me-1"></i> خطا!</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- کارت اطلاعات اشتراک -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-none border">
                <div class="card-body">
                    <h5 class="card-title">{{ $plan->name }}</h5>
                    <p class="card-text">
                        تاریخ شروع: {{ verta($subscription->starts_at)->format('Y/m/d') }}<br>
                        @if($subscription->ends_at)
                            تاریخ پایان: {{ verta($subscription->ends_at)->format('Y/m/d') }}
                            @php
                                $remainingDays = verta()->diffDays(verta($subscription->ends_at), false);
                            @endphp
                            <br>
                            @if($remainingDays > 0)
                                <span class="text-success">{{ $remainingDays }} روز باقی‌مانده</span>
                            @elseif($remainingDays == 0)
                                <span class="text-warning">امروز آخرین روز</span>
                            @else
                                <span class="text-danger">منقضی شده</span>
                            @endif
                        @else
                            تاریخ پایان: <span class="text-primary">نامحدود</span>
                        @endif
                    </p>
                    <a href="{{ route('billing.plans') }}" class="btn btn-outline-primary">
                        <i class="bx bx-up-arrow-alt"></i> ارتقا پلن
                    </a>
                </div>
            </div>
        </div>

        <!-- نمودارهای پیشرفت -->
        <div class="col-md-6 col-lg-8 mb-4">
            <div class="card shadow-none border">
                <div class="card-header">
                    <h5 class="card-title mb-0">میزان مصرف منابع</h5>
                </div>
                <div class="card-body">
                    @foreach($usageDetails as $item)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>{{ $item['label'] }}</span>
                                <span class="fw-semibold">{{ $item['used'] }} / {{ $item['limit'] }}</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $item['percent'] > 80 ? 'bg-danger' : ($item['percent'] > 50 ? 'bg-warning' : 'bg-success') }}"
                                     role="progressbar"
                                     style="width: {{ $item['percent'] }}%"
                                     aria-valuenow="{{ $item['percent'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
                                </div>
                            </div>
                            @if($item['limit'] > 0 && $item['used'] >= $item['limit'])
                                <small class="text-danger mt-1 d-block">به حداکثر رسیده است.</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection