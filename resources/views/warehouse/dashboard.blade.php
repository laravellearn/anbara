@extends('layouts.master')
@section('title', 'داشبورد انبار')

@push('styles')
<style>
.stat-card { transition: transform .2s; }
.stat-card:hover { transform: translateY(-3px); }
.chart-container { position: relative; height: 300px; }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bx bx-tachometer me-2"></i> داشبورد مدیریتی انبار</h4>
        <span class="text-muted small">آخرین بروزرسانی: {{ now()->format('Y/m/d H:i') }}</span>
    </div>

    {{-- ═══ KPI Cards ═══ --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border stat-card h-100">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-medium text-muted d-block mb-1">کل اقلام کالا</span>
                        <h2 class="mb-0">{{ number_format($kpi['total_products']) }}</h2>
                        <small class="text-muted">در {{ $kpi['total_warehouses'] }} انبار فعال</small>
                    </div>
                    <span class="badge bg-label-primary rounded p-2 mt-1"><i class="bx bx-package bx-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border stat-card h-100 {{ $kpi['pending_docs'] > 0 ? 'border-warning' : '' }}">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-medium text-muted d-block mb-1">اسناد در انتظار تأیید</span>
                        <h2 class="mb-0 {{ $kpi['pending_docs'] > 0 ? 'text-warning' : '' }}">{{ $kpi['pending_docs'] }}</h2>
                        <a href="{{ route('warehouse.documents.index', ['status' => 'pending']) }}" class="small">مشاهده اسناد</a>
                    </div>
                    <span class="badge bg-label-{{ $kpi['pending_docs'] > 0 ? 'warning' : 'secondary' }} rounded p-2 mt-1"><i class="bx bx-file bx-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border stat-card h-100 {{ $kpi['open_po'] > 0 ? 'border-info' : '' }}">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-medium text-muted d-block mb-1">سفارشات خرید باز</span>
                        <h2 class="mb-0 {{ $kpi['open_po'] > 0 ? 'text-info' : '' }}">{{ $kpi['open_po'] }}</h2>
                        <a href="{{ route('warehouse.purchase-orders.index', ['status' => 'confirmed']) }}" class="small">مشاهده PO‌ها</a>
                    </div>
                    <span class="badge bg-label-info rounded p-2 mt-1"><i class="bx bx-cart bx-md"></i></span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card shadow-none border stat-card h-100 {{ $kpi['below_min_count'] > 0 ? 'border-danger' : '' }}">
                <div class="card-body d-flex justify-content-between align-items-start">
                    <div>
                        <span class="fw-medium text-muted d-block mb-1">کالاهای زیر حداقل</span>
                        <h2 class="mb-0 {{ $kpi['below_min_count'] > 0 ? 'text-danger' : 'text-success' }}">{{ $kpi['below_min_count'] }}</h2>
                        <a href="{{ route('warehouse.reports.below-minimum') }}" class="small">مشاهده لیست</a>
                    </div>
                    <span class="badge bg-label-{{ $kpi['below_min_count'] > 0 ? 'danger' : 'success' }} rounded p-2 mt-1"><i class="bx bx-error bx-md"></i></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ماه جاری ═══ --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-none border text-center">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">ورودی ماه جاری</div>
                    <div class="fw-bold fs-4 text-success">{{ number_format($kpi['month_stock_in'], 1) }}</div>
                    <div class="text-muted small">واحد</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-none border text-center">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">خروجی ماه جاری</div>
                    <div class="fw-bold fs-4 text-danger">{{ number_format($kpi['month_stock_out'], 1) }}</div>
                    <div class="text-muted small">واحد</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-none border">
                <div class="card-body py-3">
                    <div class="text-muted small mb-2">خالص تغییر موجودی ماه</div>
                    @php $net = $kpi['month_stock_in'] - $kpi['month_stock_out']; @endphp
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height:12px">
                            <div class="progress-bar bg-success" style="width:{{ $kpi['month_stock_in'] + $kpi['month_stock_out'] > 0 ? ($kpi['month_stock_in'] / ($kpi['month_stock_in'] + $kpi['month_stock_out'])) * 100 : 50 }}%"></div>
                        </div>
                        <span class="fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 1) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ نمودار + کالاهای پرتحرک ═══ --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-none border h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="bx bx-line-chart me-1"></i> ورود / خروج ۶ ماه اخیر</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="inOutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-none border h-100">
                <div class="card-header border-bottom">
                    <h6 class="card-title mb-0"><i class="bx bx-trending-up me-1"></i> پرتحرک‌ترین کالاها (ماه جاری)</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($topProducts as $i => $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-primary rounded-circle" style="width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center">{{ $i+1 }}</span>
                                <span class="fw-medium" style="max-width:150px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $p->title }}</span>
                            </div>
                            <span class="badge bg-label-secondary">{{ number_format($p->tx_count) }} تراکنش</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">داده‌ای یافت نشد</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ آخرین اسناد + زیر حداقل ═══ --}}
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-none border">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0"><i class="bx bx-file me-1"></i> آخرین اسناد انبار</h6>
                    <a href="{{ route('warehouse.documents.index') }}" class="btn btn-sm btn-outline-primary">همه</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>شماره</th><th>نوع</th><th>انبار</th><th>تاریخ</th><th>وضعیت</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentDocs as $doc)
                            <tr>
                                <td><a href="{{ route('warehouse.documents.show', $doc) }}" class="fw-medium">{{ $doc->document_number }}</a></td>
                                <td><small>{{ $doc->type_label ?? $doc->type }}</small></td>
                                <td><small>{{ $doc->warehouse?->title ?? '—' }}</small></td>
                                <td><small>{{ $doc->created_at?->format('Y/m/d') }}</small></td>
                                <td><span class="badge bg-label-{{ $doc->status_color ?? 'secondary' }}">{{ $doc->status_label ?? $doc->status }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">سندی یافت نشد.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-none border {{ $belowMin->count() > 0 ? 'border-danger' : '' }}">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 {{ $belowMin->count() > 0 ? 'text-danger' : '' }}">
                        <i class="bx bx-error me-1"></i> زیر حداقل موجودی
                    </h6>
                    <a href="{{ route('warehouse.reports.below-minimum') }}" class="btn btn-sm btn-outline-danger">همه</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>کالا</th><th>انبار</th><th class="text-end">جاری</th><th class="text-end">حداقل</th></tr>
                        </thead>
                        <tbody>
                            @forelse($belowMin as $item)
                            <tr>
                                <td class="fw-medium" style="max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $item->product }}</td>
                                <td><small>{{ $item->warehouse }}</small></td>
                                <td class="text-end text-danger fw-bold">{{ number_format($item->current_stock, 1) }}</td>
                                <td class="text-end text-muted">{{ number_format($item->minimum_stock, 1) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-success py-4"><i class="bx bx-check-circle me-1"></i> همه موجودی‌ها مطلوب</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = @json($monthlyChart['labels']);
    const inData  = @json($monthlyChart['inData']);
    const outData = @json($monthlyChart['outData']);

    new Chart(document.getElementById('inOutChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                { label: 'ورودی', data: inData,  backgroundColor: 'rgba(40,167,69,.7)',  borderRadius: 4 },
                { label: 'خروجی', data: outData, backgroundColor: 'rgba(220,53,69,.7)', borderRadius: 4 },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: { y: { beginAtZero: true } },
        },
    });
})();
</script>
@endpush
