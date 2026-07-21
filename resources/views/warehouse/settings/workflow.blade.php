@extends('layouts.master')
@section('title', 'گردش‌کار و تأییدیه‌ها')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-git-branch me-2 text-primary"></i> گردش‌کار و تأییدیه‌ها</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @include('warehouse.settings._tabs', ['active' => 'workflow'])

    <div class="card shadow-none border">
        <div class="card-header border-bottom"><h6 class="card-title mb-0">تنظیمات فرآیند تأیید</h6></div>
        <form method="POST" action="{{ route('warehouse.settings.workflow.update') }}">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="row g-3 mb-4">
                    @php
                    $flows = [
                        ['key' => 'pr_requires_approval', 'label' => 'درخواست خرید نیاز به تأیید دارد', 'desc' => 'PR باید توسط مدیر تأیید شود تا به PO تبدیل شود'],
                        ['key' => 'ir_requires_approval', 'label' => 'درخواست کالا از انبار نیاز به تأیید دارد', 'desc' => 'IR باید تأیید شود تا حواله انبار صادر شود'],
                        ['key' => 'po_requires_approval', 'label' => 'سفارش خرید نیاز به تأیید دارد', 'desc' => 'PO باید تأیید شود قبل از ارسال به تأمین‌کننده'],
                        ['key' => 'doc_requires_approval', 'label' => 'اسناد انبار نیاز به تأیید دارند', 'desc' => 'رسید / حواله باید توسط مدیر انبار تأیید شود'],
                    ];
                    @endphp
                    @foreach($flows as $f)
                    <div class="col-md-6">
                        <div class="card shadow-none border p-3">
                            <div class="form-check form-switch d-flex justify-content-between align-items-start p-0">
                                <div>
                                    <div class="fw-medium">{{ $f['label'] }}</div>
                                    <small class="text-muted">{{ $f['desc'] }}</small>
                                </div>
                                <input class="form-check-input ms-3 mt-1" type="checkbox" name="{{ $f['key'] }}" value="1" role="switch"
                                    @checked(!empty($settings[$f['key']]) && $settings[$f['key']])>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-medium">سقف مبلغ برای تأیید خودکار (ریال)</label>
                    <input type="number" name="max_approval_amount" class="form-control" min="0" step="1000"
                        value="{{ old('max_approval_amount', $settings['max_approval_amount'] ?? 0) }}">
                    <small class="text-muted">سفارشات / درخواست‌های زیر این مبلغ نیاز به تأیید دستی ندارند (۰ = غیرفعال)</small>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i> ذخیره تغییرات</button>
            </div>
        </form>
    </div>
</div>
@endsection
