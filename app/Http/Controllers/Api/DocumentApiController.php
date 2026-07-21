<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WarehouseDocument;
use App\Services\TenantManager;
use Illuminate\Http\Request;

class DocumentApiController extends Controller
{
    public function __construct(private TenantManager $manager) {}

    public function index(Request $request)
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $query = WarehouseDocument::with(['warehouse', 'contact'])
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->latest();

        if ($request->filled('status')) { $query->where('status', $request->status); }
        if ($request->filled('type'))   { $query->where('type', $request->type); }

        $perPage = min(100, max(10, (int)$request->input('per_page', 20)));
        $docs    = $query->paginate($perPage);

        return response()->json([
            'data' => $docs->map(fn($d) => [
                'id'              => $d->id,
                'document_number' => $d->document_number,
                'type'            => $d->type,
                'status'          => $d->status,
                'warehouse'       => $d->warehouse?->title,
                'contact'         => $d->contact?->name,
                'document_date'   => $d->document_date?->format('Y-m-d'),
                'created_at'      => $d->created_at->format('Y-m-d H:i'),
            ]),
            'meta' => [
                'total'        => $docs->total(),
                'per_page'     => $docs->perPage(),
                'current_page' => $docs->currentPage(),
                'last_page'    => $docs->lastPage(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $doc = WarehouseDocument::with(['items.product', 'warehouse', 'contact'])
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->findOrFail($id);

        return response()->json([
            'id'              => $doc->id,
            'document_number' => $doc->document_number,
            'type'            => $doc->type,
            'status'          => $doc->status,
            'warehouse'       => $doc->warehouse?->title,
            'contact'         => $doc->contact?->name,
            'document_date'   => $doc->document_date?->format('Y-m-d'),
            'description'     => $doc->description,
            'items'           => $doc->items->map(fn($i) => [
                'product'  => $i->product?->title,
                'sku'      => $i->product?->sku,
                'quantity' => $i->quantity,
                'unit_price' => $i->unit_price,
            ]),
        ]);
    }
}
