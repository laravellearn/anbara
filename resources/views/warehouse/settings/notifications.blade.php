@extends('layouts.master')
@section('title', 'اعلان‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-bell me-2 text-primary"></i> تنظیمات اعلان‌ها</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @include('warehouse.settings._tabs', ['active' => 'notifications'])

    <div class="card shadow-none border">
        <div class="card-header border-bottom"><h6 class="card-title mb-0">تنظیمات اعلان و هشدار</h6></div>
        <form method="POST" action="{{ route('warehouse.settings.notifications.update') }}">
            @csrf @method('PUT')
            <div class="card-body">

                {{-- کانال اعلان --}}
                <h6 class="text-muted fw-medium mb-3">کانال‌های ارسال</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-none border p-3">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center p-0">
                                <div><div class="fw-medium"><i class="bx bx-envelope me-1"></i> ایمیل</div></div>
                                <input class="form-check-input ms-3" type="checkbox" name="notify_channel_email" value="1" role="switch"
                                    @checked(!empty($settings['notify_channel_email']) && $settings['notify_channel_email'])>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-none border p-3">
                            <div class="form-check form-switch d-flex justify-content-between align-items-center p-0">
                                <div><div class="fw-medium"><i class="bx bx-mobile me-1"></i> پیامک</div></div>
                                <input class="form-check-input ms-3" type="checkbox" name="notify_channel_sms" value="1" role="switch"
                                    @checked(!empty($settings['notify_channel_sms']) && $settings['notify_channel_sms'])>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- اطلاعات ارتباطی --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">ایمیل مدیر</label>
                        <input type="email" name="admin_email" class="form-control"
                            value="{{ old('admin_email', $settings['admin_email'] ?? '') }}" placeholder="admin@example.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">موبایل مدیر</label>
                        <input type="text" name="admin_mobile" class="form-control"
                            value="{{ old('admin_mobile', $settings['admin_mobile'] ?? '') }}" placeholder="09xxxxxxxxx">
                    </div>
                </div>

                {{-- رویدادهای اعلان --}}
                <h6 class="text-muted fw-medium mb-3">رویدادهای اعلان</h6>
                <div class="row g-3">
                    @php
                    $events = [
                        ['key' => 'notify_low_stock',   'label' => 'کمبود موجودی',           'desc' => 'هنگامی که موجودی زیر حداقل رفت'],
                        ['key' => 'notify_po_approved', 'label' => 'تأیید سفارش خرید',       'desc' => 'هنگام تأیید یا دریافت PO'],
                        ['key' => 'notify_pr_approved', 'label' => 'تأیید درخواست خرید',     'desc' => 'هنگام تأیید یا رد PR'],
                        ['key' => 'notify_ir_approved', 'label' => 'تأیید درخواست کالا',     'desc' => 'هنگام تأیید یا رد IR'],
                        ['key' => 'notify_doc_pending', 'label' => 'اسناد در انتظار تأیید',  'desc' => 'اسناد انبار که نیاز به تأیید دارند'],
                    ];
                    @endphp
                    @foreach($events as $e)
                    <div class="col-md-6">
                        <div class="card shadow-none border p-3">
                            <div class="form-check form-switch d-flex justify-content-between align-items-start p-0">
                                <div>
                                    <div class="fw-medium">{{ $e['label'] }}</div>
                                    <small class="text-muted">{{ $e['desc'] }}</small>
                                </div>
                                <input class="form-check-input ms-3 mt-1" type="checkbox" name="{{ $e['key'] }}" value="1" role="switch"
                                    @checked(!empty($settings[$e['key']]) && $settings[$e['key']])>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
