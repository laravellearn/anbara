@extends('layouts.master')
@section('title', 'تنظیمات انبار')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-cog me-2 text-primary"></i> تنظیمات انبار</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @include('warehouse.settings._tabs', ['active' => 'warehouse'])

    <div class="card shadow-none border">
        <div class="card-header border-bottom"><h6 class="card-title mb-0">تنظیمات عملیاتی انبار</h6></div>
        <form method="POST" action="{{ route('warehouse.settings.warehouse.update') }}">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">روش ارزش‌گذاری پیش‌فرض موجودی</label>
                        <select name="default_valuation_method" class="form-select">
                            <option value="fifo" @selected(($settings['default_valuation_method'] ?? 'fifo') === 'fifo')>FIFO — اول وارد، اول صادر</option>
                            <option value="lifo" @selected(($settings['default_valuation_method'] ?? '') === 'lifo')>LIFO — آخر وارد، اول صادر</option>
                            <option value="average" @selected(($settings['default_valuation_method'] ?? '') === 'average')>میانگین متحرک</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">آستانه هشدار موجودی کم</label>
                        <input type="number" name="low_stock_alert_threshold" class="form-control"
                            min="0" value="{{ old('low_stock_alert_threshold', $settings['low_stock_alert_threshold'] ?? 0) }}">
                        <small class="text-muted">واحد: درصد زیر حداقل موجودی تعریف‌شده</small>
                    </div>

                    <div class="col-12">
                        <h6 class="fw-medium mb-3 text-muted">گزینه‌های عملیاتی</h6>
                        <div class="row g-3">
                            @php
                            $toggles = [
                                ['key' => 'auto_approve_documents', 'label' => 'تأیید خودکار اسناد انبار', 'desc' => 'اسناد بدون نیاز به تأیید دستی ثبت می‌شوند'],
                                ['key' => 'allow_negative_stock', 'label' => 'اجازه موجودی منفی', 'desc' => 'امکان صدور حواله حتی با موجودی صفر'],
                                ['key' => 'require_reason_for_adjustment', 'label' => 'دلیل اجباری برای تعدیل', 'desc' => 'در عملیات تعدیل موجودی، دلیل الزامی است'],
                                ['key' => 'low_stock_alert_enabled', 'label' => 'فعال‌سازی هشدار کمبود موجودی', 'desc' => 'نمایش هشدار در داشبورد و گزارشات'],
                            ];
                            @endphp
                            @foreach($toggles as $t)
                            <div class="col-md-6">
                                <div class="card shadow-none border p-3">
                                    <div class="form-check form-switch d-flex justify-content-between align-items-start p-0">
                                        <div>
                                            <div class="fw-medium">{{ $t['label'] }}</div>
                                            <small class="text-muted">{{ $t['desc'] }}</small>
                                        </div>
                                        <input class="form-check-input ms-3 mt-1" type="checkbox" name="{{ $t['key'] }}" value="1" role="switch"
                                            @checked(!empty($settings[$t['key']]) && $settings[$t['key']])>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
