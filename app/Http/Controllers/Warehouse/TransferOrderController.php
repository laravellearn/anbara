<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\TransferOrder;
use App\Models\TransferOrderItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\MeasurementUnit;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransferOrderController extends BaseController
{
    // ─── لیست ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'transfers.view');
        [$tenantId, $companyId] = $this->tenantCtx();

        $query = $this->fyQuery(TransferOrder::class, $tenantId, $companyId)
            ->with(['fromWarehouse','toWarehouse','creator'])
            ->latest();

        if ($request->filled('status'))       $query->where('status', $request->status);
        if ($request->filled('warehouse_id')) {
            $query->where(fn($q) =>
                $q->where('from_warehouse_id', $request->warehouse_id)
                  ->orWhere('to_warehouse_id', $request->warehouse_id)
            );
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('transfer_number', 'like', "%{$s}%");
        }

        $transfers  = $query->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $stats = [
            'total'      => $this->fyQuery(TransferOrder::class, $tenantId, $companyId)->count(),
            'draft'      => $this->fyQuery(TransferOrder::class, $tenantId, $companyId)->where('status','draft')->count(),
            'in_transit' => $this->fyQuery(TransferOrder::class, $tenantId, $companyId)->where('status','in_transit')->count(),
            'completed'  => $this->fyQuery(TransferOrder::class, $tenantId, $companyId)->where('status','completed')->count(),
        ];

        return view('warehouse.transfers.index', compact('transfers','warehouses','stats'));
    }

    // ─── فرم ایجاد ────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'transfers.create');
        [$tenantId] = $this->tenantCtx();

        $warehouses = Warehouse::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $products   = Product::where('tenant_id', $tenantId)->where('is_active', true)->orderBy('title')->get();
        $units      = MeasurementUnit::where('tenant_id', $tenantId)->get();
        $number     = $this->generateNumber($tenantId);

        return view('warehouse.transfers.create', compact('warehouses','products','units','number'));
    }

    // ─── ذخیره ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        Gate::authorize('access', 'transfers.create');
        [$tenantId, $companyId] = $this->tenantCtx();

        $request->validate([
            'from_warehouse_id'  => 'required|exists:warehouses,id',
            'to_warehouse_id'    => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date'      => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|numeric|min:0.0001',
        ]);

        $transfer = DB::transaction(function () use ($request, $tenantId, $companyId) {
            $fy = $this->manager->getFiscalYear();

            $transfer = TransferOrder::create([
                'tenant_id'          => $tenantId,
                'company_id'         => $companyId,
                'fiscal_year_id'     => $fy?->id,
                'transfer_number'    => $this->generateNumber($tenantId),
                'status'             => 'draft',
                'from_warehouse_id'  => $request->from_warehouse_id,
                'to_warehouse_id'    => $request->to_warehouse_id,
                'transfer_date'      => $request->transfer_date,
                'expected_arrival_date' => $request->expected_arrival_date,
                'reason'             => $request->reason,
                'notes'              => $request->notes,
                'created_by'         => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                TransferOrderItem::create([
                    'transfer_order_id'   => $transfer->id,
                    'product_id'          => $item['product_id'],
                    'measurement_unit_id' => $item['unit_id'] ?? null,
                    'quantity_requested'  => $item['quantity'],
                    'unit_price'          => $item['unit_price'] ?? null,
                    'notes'               => $item['notes'] ?? null,
                ]);
            }

            return $transfer;
        });

        return redirect()->route('warehouse.transfers.show', $transfer)
            ->with('success', 'سند انتقال با موفقیت ایجاد شد.');
    }

    // ─── نمایش ────────────────────────────────────────────────────────────────
    public function show(TransferOrder $transfer)
    {
        Gate::authorize('access', 'transfers.view');
        $this->authorizeTransfer($transfer);

        $transfer->load(['items.product','items.measurementUnit','fromWarehouse','toWarehouse','creator','confirmer','completer']);
        return view('warehouse.transfers.show', compact('transfer'));
    }

    // ─── تأیید ────────────────────────────────────────────────────────────────
    public function confirm(TransferOrder $transfer)
    {
        Gate::authorize('access', 'transfers.confirm');
        $this->authorizeTransfer($transfer);

        if (!$transfer->canConfirm()) {
            return back()->with('error', 'این سند قابل تأیید نیست.');
        }

        $transfer->update([
            'status'       => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('success', 'سند انتقال تأیید شد.');
    }

    // ─── شروع حمل ────────────────────────────────────────────────────────────
    public function transit(TransferOrder $transfer)
    {
        Gate::authorize('access', 'transfers.confirm');
        $this->authorizeTransfer($transfer);

        if (!$transfer->canTransit()) return back()->with('error', 'وضعیت نامعتبر.');

        $transfer->update(['status' => 'in_transit']);
        return back()->with('success', 'سند در حال انتقال است.');
    }

    // ─── تکمیل + ثبت تراکنش انبار ────────────────────────────────────────────
    public function complete(Request $request, TransferOrder $transfer)
    {
        Gate::authorize('access', 'transfers.confirm');
        $this->authorizeTransfer($transfer);

        if (!$transfer->canComplete()) return back()->with('error', 'این سند قابل تکمیل نیست.');

        DB::transaction(function () use ($request, $transfer) {
            $tenantId  = $transfer->tenant_id;
            $companyId = $transfer->company_id;
            $fyId      = $transfer->fiscal_year_id;

            foreach ($transfer->items as $item) {
                $qty = $request->input("quantities.{$item->id}", $item->quantity_requested);

                // خروج از انبار مبدأ
                StockTransaction::create([
                    'tenant_id'       => $tenantId,
                    'company_id'      => $companyId,
                    'fiscal_year_id'  => $fyId,
                    'warehouse_id'    => $transfer->from_warehouse_id,
                    'product_id'      => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'type'            => 'transfer_out',
                    'status'          => 'approved',
                    'quantity'        => $qty,
                    'unit_price'      => $item->unit_price,
                    'description'     => "انتقال به انبار — {$transfer->transfer_number}",
                    'reference_type'  => TransferOrder::class,
                    'reference_id'    => $transfer->id,
                    'user_id'         => auth()->id(),
                ]);

                // ورود به انبار مقصد
                StockTransaction::create([
                    'tenant_id'       => $tenantId,
                    'company_id'      => $companyId,
                    'fiscal_year_id'  => $fyId,
                    'warehouse_id'    => $transfer->to_warehouse_id,
                    'product_id'      => $item->product_id,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'type'            => 'transfer_in',
                    'status'          => 'approved',
                    'quantity'        => $qty,
                    'unit_price'      => $item->unit_price,
                    'description'     => "دریافت از انتقال — {$transfer->transfer_number}",
                    'reference_type'  => TransferOrder::class,
                    'reference_id'    => $transfer->id,
                    'user_id'         => auth()->id(),
                ]);

                $item->update(['quantity_transferred' => $qty]);
            }

            $transfer->update([
                'status'               => 'completed',
                'actual_arrival_date'  => now()->toDateString(),
                'completed_by'         => auth()->id(),
                'completed_at'         => now(),
            ]);
        });

        return back()->with('success', 'انتقال با موفقیت تکمیل شد و تراکنش‌های انبار ثبت شدند.');
    }

    // ─── لغو ─────────────────────────────────────────────────────────────────
    public function cancel(TransferOrder $transfer)
    {
        Gate::authorize('access', 'transfers.cancel');
        $this->authorizeTransfer($transfer);

        if (!$transfer->canCancel()) return back()->with('error', 'این سند قابل لغو نیست.');

        $transfer->update(['status' => 'cancelled']);
        return back()->with('success', 'سند انتقال لغو شد.');
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function authorizeTransfer(TransferOrder $transfer): void
    {
        [$tenantId, $companyId] = $this->tenantCtx();
        abort_if($transfer->tenant_id !== $tenantId, 403);
    }

    private function generateNumber(int $tenantId): string
    {
        $count = TransferOrder::where('tenant_id', $tenantId)->count() + 1;
        return 'TRF-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
