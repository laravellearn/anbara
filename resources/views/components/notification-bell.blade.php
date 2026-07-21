{{-- کامپوننت ناقوس اعلان‌ها — در navbar قرار می‌گیرد --}}
@php
    use Illuminate\Support\Facades\DB;
    $tenantId  = auth()->user()?->tenant_id  ?? 0;
    $companyId = auth()->user()?->company_id ?? 0;

    // اسناد در انتظار تأیید
    $pendingDocs = \App\Models\WarehouseDocument::where('tenant_id', $tenantId)
        ->where('company_id', $companyId)->where('status', 'pending')->count();

    // سفارشات خرید باز
    $openPos = \App\Models\PurchaseOrder::where('tenant_id', $tenantId)
        ->where('company_id', $companyId)->whereIn('status', ['confirmed','sent','partial_received'])->count();

    // کالاهای زیر حداقل
    $belowMin = DB::table('stock_transactions as st')
        ->join('products as p','p.id','=','st.product_id')
        ->where('st.tenant_id', $tenantId)->where('st.company_id', $companyId)
        ->where('st.status','approved')->where('p.minimum_stock','>',0)
        ->groupBy('p.id','p.title','p.minimum_stock','st.warehouse_id')
        ->havingRaw("
            SUM(CASE WHEN st.type IN ('purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in') THEN st.quantity ELSE 0 END)
          - SUM(CASE WHEN st.type IN ('issue','return_to_supplier','transfer_out','adjustment_out','return_out') THEN st.quantity ELSE 0 END)
          < p.minimum_stock
        ")
        ->select('p.id','p.title',
            DB::raw("SUM(CASE WHEN st.type IN ('purchase_receipt','return_from_customer','opening','transfer_in','adjustment_in','receipt','return_in') THEN st.quantity ELSE 0 END)
                   - SUM(CASE WHEN st.type IN ('issue','return_to_supplier','transfer_out','adjustment_out','return_out') THEN st.quantity ELSE 0 END) as current_stock"),
            'p.minimum_stock'
        )
        ->limit(5)->get();

    $totalBell = $pendingDocs + $openPos + $belowMin->count();
@endphp

<li class="nav-item navbar-dropdown dropdown-user dropdown">
    <a class="nav-link dropdown-toggle hide-arrow position-relative p-0 me-2" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="bx bx-bell bx-sm"></i>
        @if($totalBell > 0)
        <span class="badge bg-danger rounded-pill position-absolute"
              style="top:-4px;left:-4px;font-size:9px;min-width:16px;height:16px;line-height:16px;padding:0 4px">
            {{ $totalBell > 99 ? '99+' : $totalBell }}
        </span>
        @endif
    </a>
    <ul class="dropdown-menu dropdown-menu-end" style="min-width:320px;max-height:480px;overflow-y:auto">
        <li>
            <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <span class="fw-semibold">اعلان‌ها</span>
                @if($totalBell > 0)
                <span class="badge bg-danger">{{ $totalBell }}</span>
                @else
                <span class="text-muted small">همه‌چیز مرتب است ✓</span>
                @endif
            </div>
        </li>

        {{-- اسناد در انتظار --}}
        @if($pendingDocs > 0)
        <li>
            <a href="{{ route('warehouse.documents.index', ['status'=>'pending']) }}" class="dropdown-item py-2">
                <div class="d-flex gap-2 align-items-start">
                    <span class="badge bg-label-warning rounded p-1 mt-1"><i class="bx bx-file bx-xs"></i></span>
                    <div>
                        <div class="fw-medium">{{ $pendingDocs }} سند در انتظار تأیید</div>
                        <small class="text-muted">اسناد انبار منتظر تأیید شما</small>
                    </div>
                </div>
            </a>
        </li>
        @endif

        {{-- سفارشات خرید باز --}}
        @if($openPos > 0)
        <li>
            <a href="{{ route('warehouse.purchase-orders.index', ['status'=>'confirmed']) }}" class="dropdown-item py-2">
                <div class="d-flex gap-2 align-items-start">
                    <span class="badge bg-label-info rounded p-1 mt-1"><i class="bx bx-cart bx-xs"></i></span>
                    <div>
                        <div class="fw-medium">{{ $openPos }} سفارش خرید باز</div>
                        <small class="text-muted">منتظر دریافت یا پیگیری</small>
                    </div>
                </div>
            </a>
        </li>
        @endif

        {{-- کالاهای زیر حداقل --}}
        @foreach($belowMin as $item)
        <li>
            <a href="{{ route('warehouse.reports.below-minimum') }}" class="dropdown-item py-2">
                <div class="d-flex gap-2 align-items-start">
                    <span class="badge bg-label-danger rounded p-1 mt-1"><i class="bx bx-error bx-xs"></i></span>
                    <div>
                        <div class="fw-medium text-truncate" style="max-width:220px">{{ $item->title }}</div>
                        <small class="text-danger">
                            موجودی: {{ number_format($item->current_stock, 1) }} | حداقل: {{ number_format($item->minimum_stock, 1) }}
                        </small>
                    </div>
                </div>
            </a>
        </li>
        @endforeach

        @if($totalBell === 0)
        <li>
            <div class="text-center text-muted py-4">
                <i class="bx bx-check-shield bx-md text-success"></i><br>
                <small>هیچ اعلان فعالی وجود ندارد</small>
            </div>
        </li>
        @endif

        <li><hr class="dropdown-divider"></li>
        <li>
            <div class="d-flex gap-2 px-3 py-2">
                <a href="{{ route('warehouse.reports.below-minimum') }}" class="btn btn-sm btn-outline-danger flex-grow-1">
                    <i class="bx bx-error me-1"></i> زیر حداقل
                </a>
                <a href="{{ route('warehouse.documents.index', ['status'=>'pending']) }}" class="btn btn-sm btn-outline-warning flex-grow-1">
                    <i class="bx bx-file me-1"></i> اسناد باز
                </a>
            </div>
        </li>
    </ul>
</li>
