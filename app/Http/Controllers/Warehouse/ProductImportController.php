<?php

namespace App\Http\Controllers\Warehouse;

use App\Models\Category;
use App\Models\MeasurementUnit;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

/**
 * ایمپورت انبوه کالا از فایل CSV/Excel
 */
class ProductImportController extends BaseController
{
    public function form()
    {
        Gate::authorize('access', 'products.create');
        return view('warehouse.products.import');
    }

    public function import(Request $request)
    {
        Gate::authorize('access', 'products.create');

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:5120'],
        ]);

        $tenantId  = $this->manager->getTenantId();
        $companyId = $this->manager->getCompanyId();

        $file = $request->file('file');
        $rows = $this->parseCsv($file->getRealPath());

        if (empty($rows)) {
            return back()->withErrors(['file' => 'فایل خالی است یا فرمت اشتباه دارد.']);
        }

        // نگاشت دسته‌بندی و واحد اندازه‌گیری
        $categories = Category::where('tenant_id', $tenantId)->pluck('id', 'title');
        $units      = MeasurementUnit::where('tenant_id', $tenantId)->pluck('id', 'title');

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + 2; // شماره ردیف در فایل (با احتساب هدر)

            $v = Validator::make($row, [
                'عنوان'   => ['required', 'string', 'max:255'],
                'کد_کالا' => ['nullable', 'string', 'max:100'],
            ]);

            if ($v->fails()) {
                $errors[] = "ردیف {$rowNum}: " . implode(' | ', $v->errors()->all());
                $skipped++;
                continue;
            }

            $sku = $row['کد_کالا'] ?? null;

            // بررسی تکراری نبودن SKU
            if ($sku && Product::where('tenant_id', $tenantId)->where('sku', $sku)->exists()) {
                $errors[] = "ردیف {$rowNum}: کد کالا «{$sku}» قبلاً ثبت شده — رد شد.";
                $skipped++;
                continue;
            }

            $categoryId = null;
            if (!empty($row['دسته‌بندی'])) {
                $categoryId = $categories[$row['دسته‌بندی']] ?? null;
            }

            $unitId = null;
            if (!empty($row['واحد'])) {
                $unitId = $units[$row['واحد']] ?? null;
            }

            Product::create([
                'tenant_id'           => $tenantId,
                'company_id'          => $companyId,
                'title'               => $row['عنوان'],
                'sku'                 => $sku,
                'barcode'             => $row['بارکد']        ?? null,
                'model'               => $row['مدل']          ?? null,
                'description'         => $row['توضیحات']      ?? null,
                'minimum_stock'       => (float)($row['حداقل_موجودی'] ?? 0),
                'maximum_stock'       => ($row['حداکثر_موجودی'] ?? null) ? (float)$row['حداکثر_موجودی'] : null,
                'category_id'         => $categoryId,
                'measurement_unit_id' => $unitId,
                'is_active'           => true,
            ]);

            $imported++;
        }

        $msg = "✅ {$imported} کالا وارد شد.";
        if ($skipped) {
            $msg .= " | ⚠️ {$skipped} ردیف رد شد.";
        }

        return redirect()->route('warehouse.products.index')
            ->with('toast', ['type' => $skipped ? 'warning' : 'success', 'message' => $msg])
            ->with('import_errors', $errors);
    }

    /** دانلود قالب CSV نمونه */
    public function template()
    {
        $headers = ['عنوان','کد_کالا','بارکد','مدل','دسته‌بندی','واحد','حداقل_موجودی','حداکثر_موجودی','توضیحات'];
        $sample  = ['کابل برق ۱۰ متری','CBL-001','1234567890','مدل A','برق و الکترونیک','عدد','10','100','کابل مسی استاندارد'];

        return response()->streamDownload(function () use ($headers, $sample) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM فارسی
            fputcsv($out, $headers);
            fputcsv($out, $sample);
            fclose($out);
        }, 'product-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ─── helper ───────────────────────────────────────────────────────────────
    private function parseCsv(string $path): array
    {
        $rows    = [];
        $handle  = fopen($path, 'r');
        if (!$handle) return [];

        // حذف BOM اگر وجود داشت
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            fseek($handle, 0);
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        // نرمال‌سازی هدرها
        $headers = array_map(fn($h) => trim($h), $headers);

        while (($cols = fgetcsv($handle)) !== false) {
            if (count($cols) !== count($headers)) continue;
            $rows[] = array_combine($headers, $cols);
        }

        fclose($handle);
        return $rows;
    }
}
