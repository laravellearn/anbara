<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Product;
use App\Models\SerialBatch;
use App\Models\Warehouse;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportController extends BaseController
{
    public function __construct(
        \App\Services\TenantManager $manager,
        private ImportService $importService
    ) {
        parent::__construct($manager);
    }

    public function index()
    {
        Gate::authorize('access', 'import.products');
        return view('warehouse.import.index');
    }

    /** دانلود قالب CSV کالاها */
    public function productTemplate(): StreamedResponse
    {
        Gate::authorize('access', 'import.products');
        return response()->streamDownload(function () {
            echo ImportService::productTemplate();
        }, 'products_template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** وارد کردن کالاها */
    public function importProducts(Request $request)
    {
        Gate::authorize('access', 'import.products');
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        [$tenantId, $companyId] = $this->ctx();
        $result = $this->importService->importProducts(
            $request->file('file'),
            $tenantId,
            $companyId,
            auth()->id()
        );

        return back()->with('import_result', $result);
    }
}
