<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\PurchaseOrder;
use App\Models\WarehouseDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * کارتابل تأیید — نمایش همه اسناد و سفارشات در انتظار تأیید مدیر
 */
class ApprovalInboxController extends BaseController
{
    public function index(Request $request)
    {
        Gate::authorize('access', 'approval-inbox.view');

        [$tenantId, $companyId] = [$this->manager->getTenantId(), $this->manager->getCompanyId()];

        $filter = $request->get('type', 'all'); // all | documents | purchase_orders

        // اسناد انبار در انتظار تأیید
        $pendingDocs = collect();
        if ($filter === 'all' || $filter === 'documents') {
            $pendingDocs = WarehouseDocument::with(['warehouse', 'creator'])
                ->where('tenant_id', $tenantId)
                ->where('company_id', $companyId)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        // سفارشات خرید در انتظار تأیید (پیش‌نویس)
        $pendingPos = collect();
        if ($filter === 'all' || $filter === 'purchase_orders') {
            $pendingPos = PurchaseOrder::with(['supplier', 'warehouse', 'creator'])
                ->where('tenant_id', $tenantId)
                ->where('company_id', $companyId)
                ->where('status', PurchaseOrder::STATUS_DRAFT)
                ->latest()
                ->get();
        }

        $counts = [
            'docs' => WarehouseDocument::where('tenant_id', $tenantId)
                ->where('company_id', $companyId)->where('status', 'pending')->count(),
            'pos'  => PurchaseOrder::where('tenant_id', $tenantId)
                ->where('company_id', $companyId)->where('status', PurchaseOrder::STATUS_DRAFT)->count(),
        ];

        return view('warehouse.approval-inbox.index', compact(
            'pendingDocs', 'pendingPos', 'counts', 'filter'
        ));
    }

    /** تأیید سریع سند انبار از کارتابل */
    public function approveDocument(WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.approve');
        $this->authorizeDoc($document);

        if ($document->status !== 'pending') {
            return back()->with('toast', ['type' => 'warning', 'message' => 'سند قابل تأیید نیست.']);
        }

        $document->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'سند انبار تأیید شد.']);
    }

    /** تأیید سریع سفارش خرید از کارتابل */
    public function approvePo(PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);

        if (!$purchaseOrder->canConfirm()) {
            return back()->with('toast', ['type' => 'warning', 'message' => 'سفارش قابل تأیید نیست.']);
        }

        $purchaseOrder->update([
            'status'       => PurchaseOrder::STATUS_CONFIRMED,
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'سفارش خرید تأیید شد.']);
    }

    /** رد سند انبار از کارتابل */
    public function rejectDocument(Request $request, WarehouseDocument $document)
    {
        Gate::authorize('access', 'warehouse-documents.approve');
        $this->authorizeDoc($document);

        $document->update([
            'status' => 'rejected',
            'notes'  => ($document->notes ? $document->notes . "\n" : '') . 'رد شد: ' . $request->input('reason', ''),
        ]);

        return back()->with('toast', ['type' => 'danger', 'message' => 'سند انبار رد شد.']);
    }

    /** رد سفارش خرید از کارتابل */
    public function rejectPo(Request $request, PurchaseOrder $purchaseOrder)
    {
        Gate::authorize('access', 'purchase-orders.confirm');
        $this->authorizePo($purchaseOrder);

        $purchaseOrder->update([
            'status'              => PurchaseOrder::STATUS_CANCELLED,
            'cancellation_reason' => $request->input('reason', ''),
        ]);

        return back()->with('toast', ['type' => 'danger', 'message' => 'سفارش خرید رد شد.']);
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function authorizeDoc(WarehouseDocument $doc): void
    {
        if ($doc->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }
    }

    private function authorizePo(PurchaseOrder $po): void
    {
        if ($po->tenant_id !== $this->manager->getTenantId()) {
            abort(403);
        }
    }
}
