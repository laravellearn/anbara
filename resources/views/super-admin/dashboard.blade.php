@extends('super-admin.layouts.master')
@section('title', 'داشبورد مدیریت کل')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">کل سازمان‌ها</h5>
                <p class="display-4 text-primary">{{ $totalTenants }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">سازمان‌های فعال</h5>
                <p class="display-4 text-success">{{ $activeTenants }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">اشتراک‌های فعال</h5>
                <p class="display-4 text-warning">{{ $totalSubscriptions }}</p>
            </div>
        </div>
    </div>
</div>
@endsection