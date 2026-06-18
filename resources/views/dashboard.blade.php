@extends('layouts.master')
@section('title', 'داشبورد')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کالاها</span>
                            <h3 class="mb-0 mt-1">{{ $productsCount }}</h3>
                        </div>
                        <span class="badge bg-label-primary rounded p-2"><i class="bx bx-package bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">انبارها</span>
                            <h3 class="mb-0 mt-1">{{ $warehousesCount }}</h3>
                        </div>
                        <span class="badge bg-label-warning rounded p-2"><i class="bx bx-buildings bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">کاربران</span>
                            <h3 class="mb-0 mt-1">{{ $usersCount }}</h3>
                        </div>
                        <span class="badge bg-label-success rounded p-2"><i class="bx bx-group bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span class="fw-medium text-muted">دسته‌بندی‌ها</span>
                            <h3 class="mb-0 mt-1">{{ $categoriesCount }}</h3>
                        </div>
                        <span class="badge bg-label-info rounded p-2"><i class="bx bx-category bx-sm"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-none border">
                <div class="card-header"><h5>فعالیت‌های اخیر</h5></div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr><th>زمان</th><th>کاربر</th><th>عملیات</th><th>شرح</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivities as $log)
                            <tr>
                                <td>{{ verta($log->created_at)->format('Y/m/d H:i') }}</td>
                                <td>{{ $log->user?->name ?? '---' }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->description }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-none border">
                <div class="card-body">
                    <h5>اشتراک فعلی</h5>
                    @if($activeSubscription)
                        <span class="badge bg-label-success">{{ $activeSubscription->plan->name }}</span>
                        @if($activeSubscription->ends_at)
                            @php $remainingDays = \Verta::now()->diffDays(\Verta::instance($activeSubscription->ends_at), false); @endphp
                            <p class="mt-2">{{ $remainingDays > 0 ? $remainingDays.' روز باقی‌مانده' : 'امروز آخرین روز' }}</p>
                        @endif
                    @else
                        <span class="text-danger">بدون اشتراک</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection