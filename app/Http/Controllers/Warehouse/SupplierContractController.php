<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\SupplierContract;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class SupplierContractController extends BaseController
{
    // ─── لیست ────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('access', 'supplier-contracts.view');
        [$tenantId, $companyId] = $this->tenantCtx();

        $query = $this->fyQuery(SupplierContract::class, $tenantId, $companyId)
            ->with(['supplier','creator'])->latest();

        if ($request->filled('status'))      $query->where('status', $request->status);
        if ($request->filled('supplier_id')) $query->where('supplier_id', $request->supplier_id);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('contract_number','like',"%{$s}%")->orWhere('title','like',"%{$s}%"));
        }

        // به‌روزرسانی خودکار وضعیت منقضی‌شده
        SupplierContract::forTenant($tenantId, $companyId)
            ->where('status', 'active')
            ->where('end_date', '<', now())
            ->update(['status' => 'expired']);

        $contracts = $query->paginate(20)->withQueryString();
        $suppliers = Contact::where('tenant_id', $tenantId)->where('type','supplier')->orderBy('name')->get();
        $stats = [
            'total'    => $this->fyQuery(SupplierContract::class, $tenantId, $companyId)->count(),
            'active'   => $this->fyQuery(SupplierContract::class, $tenantId, $companyId)->where('status','active')->count(),
            'expiring' => SupplierContract::forTenant($tenantId, $companyId)->expiring(30)->count(),
            'expired'  => $this->fyQuery(SupplierContract::class, $tenantId, $companyId)->where('status','expired')->count(),
        ];

        return view('warehouse.supplier-contracts.index', compact('contracts','suppliers','stats'));
    }

    // ─── ایجاد ────────────────────────────────────────────────────────────────
    public function create()
    {
        Gate::authorize('access', 'supplier-contracts.create');
        [$tenantId] = $this->tenantCtx();
        $suppliers = Contact::where('tenant_id', $tenantId)->where('type','supplier')->orderBy('name')->get();
        $number    = $this->generateNumber($tenantId);
        return view('warehouse.supplier-contracts.create', compact('suppliers','number'));
    }

    public function store(Request $request)
    {
        Gate::authorize('access', 'supplier-contracts.create');
        [$tenantId, $companyId] = $this->tenantCtx();

        $request->validate([
            'supplier_id'       => 'required|exists:contacts,id',
            'title'             => 'required|string|max:255',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after:start_date',
            'credit_limit'      => 'nullable|numeric|min:0',
            'payment_terms_days'=> 'nullable|integer|min:0',
            'discount_percent'  => 'nullable|numeric|min:0|max:100',
            'file'              => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $fy = $this->manager->getFiscalYear();
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("contracts/{$tenantId}", 'local');
        }

        SupplierContract::create([
            'tenant_id'           => $tenantId,
            'company_id'          => $companyId,
            'fiscal_year_id'      => $fy?->id,
            'contract_number'     => $this->generateNumber($tenantId),
            'status'              => 'active',
            'supplier_id'         => $request->supplier_id,
            'title'               => $request->title,
            'start_date'          => $request->start_date,
            'end_date'            => $request->end_date,
            'credit_limit'        => $request->credit_limit ?? 0,
            'payment_terms_days'  => $request->payment_terms_days ?? 30,
            'discount_percent'    => $request->discount_percent ?? 0,
            'terms_and_conditions'=> $request->terms_and_conditions,
            'notes'               => $request->notes,
            'file_path'           => $filePath,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('warehouse.supplier-contracts.index')
            ->with('success', 'قرارداد با موفقیت ثبت شد.');
    }

    // ─── نمایش ────────────────────────────────────────────────────────────────
    public function show(SupplierContract $supplierContract)
    {
        Gate::authorize('access', 'supplier-contracts.view');
        abort_if($supplierContract->tenant_id !== $this->manager->getTenantId(), 403);
        $supplierContract->load(['supplier','creator']);
        return view('warehouse.supplier-contracts.show', compact('supplierContract'));
    }

    // ─── ویرایش ───────────────────────────────────────────────────────────────
    public function edit(SupplierContract $supplierContract)
    {
        Gate::authorize('access', 'supplier-contracts.create');
        abort_if($supplierContract->tenant_id !== $this->manager->getTenantId(), 403);
        [$tenantId] = $this->tenantCtx();
        $suppliers = Contact::where('tenant_id', $tenantId)->where('type','supplier')->orderBy('name')->get();
        return view('warehouse.supplier-contracts.edit', compact('supplierContract','suppliers'));
    }

    public function update(Request $request, SupplierContract $supplierContract)
    {
        Gate::authorize('access', 'supplier-contracts.create');
        abort_if($supplierContract->tenant_id !== $this->manager->getTenantId(), 403);

        $request->validate([
            'title'              => 'required|string|max:255',
            'start_date'         => 'required|date',
            'end_date'           => 'required|date|after:start_date',
            'credit_limit'       => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'discount_percent'   => 'nullable|numeric|min:0|max:100',
            'file'               => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $data = $request->only(['title','start_date','end_date','credit_limit',
            'payment_terms_days','discount_percent','terms_and_conditions','notes','status']);

        if ($request->hasFile('file')) {
            if ($supplierContract->file_path) Storage::disk('local')->delete($supplierContract->file_path);
            $data['file_path'] = $request->file('file')->store("contracts/{$supplierContract->tenant_id}", 'local');
        }

        $supplierContract->update($data);
        return redirect()->route('warehouse.supplier-contracts.show', $supplierContract)
            ->with('success', 'قرارداد به‌روزرسانی شد.');
    }

    // ─── فسخ قرارداد ─────────────────────────────────────────────────────────
    public function terminate(SupplierContract $supplierContract)
    {
        Gate::authorize('access', 'supplier-contracts.create');
        abort_if($supplierContract->tenant_id !== $this->manager->getTenantId(), 403);
        $supplierContract->update(['status' => 'terminated']);
        return back()->with('success', 'قرارداد فسخ شد.');
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    private function tenantCtx(): array
    {
        return [$this->manager->getTenantId(), $this->manager->getCompanyId()];
    }

    private function generateNumber(int $tenantId): string
    {
        $count = SupplierContract::where('tenant_id', $tenantId)->withTrashed()->count() + 1;
        return 'CNT-' . now()->format('Ym') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
