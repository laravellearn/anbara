@extends('layouts.master')
@section('title', 'شماره‌گذاری اسناد')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0"><i class="bx bx-hash me-2 text-primary"></i> شماره‌گذاری اسناد</h4>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4"><i class="bx bx-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @include('warehouse.settings._tabs', ['active' => 'numbering'])

    <div class="card shadow-none border">
        <div class="card-header border-bottom"><h6 class="card-title mb-0">قالب شماره‌گذاری اسناد</h6></div>
        <form method="POST" action="{{ route('warehouse.settings.numbering.update') }}">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">طول عدد سریال</label>
                        <input type="number" name="number_length" class="form-control" min="3" max="10"
                            value="{{ old('number_length', $settings['number_length'] ?? 5) }}" required>
                        <small class="text-muted">مثال: ۵ = ۰۰۰۰۱</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="include_year" value="1" role="switch"
                                @checked(!empty($settings['include_year']) && $settings['include_year']) id="incYear">
                            <label class="form-check-label" for="incYear">درج سال در شماره</label>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="include_month" value="1" role="switch"
                                @checked(!empty($settings['include_month']) && $settings['include_month']) id="incMonth">
                            <label class="form-check-label" for="incMonth">درج ماه در شماره</label>
                        </div>
                    </div>
                </div>

                <h6 class="text-muted fw-medium mb-3">پیشوندهای اسناد</h6>
                <div class="row g-3">
                    @php
                    $prefixes = [
                        ['key' => 'po_prefix',  'label' => 'سفارش خرید (PO)',        'default' => 'PO'],
                        ['key' => 'pr_prefix',  'label' => 'درخواست خرید (PR)',      'default' => 'PR'],
                        ['key' => 'ir_prefix',  'label' => 'درخواست کالا (IR)',       'default' => 'IR'],
                        ['key' => 'inv_prefix', 'label' => 'فاکتور خرید (INV)',       'default' => 'INV'],
                        ['key' => 'doc_prefix', 'label' => 'اسناد انبار (DOC)',       'default' => 'DOC'],
                    ];
                    @endphp
                    @foreach($prefixes as $p)
                    <div class="col-md-4">
                        <label class="form-label fw-medium">{{ $p['label'] }}</label>
                        <div class="input-group">
                            <input type="text" name="{{ $p['key'] }}" class="form-control" maxlength="10"
                                value="{{ old($p['key'], $settings[$p['key']] ?? $p['default']) }}">
                            <span class="input-group-text text-muted">-YYYYMM-00001</span>
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
