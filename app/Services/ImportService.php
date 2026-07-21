<?php

namespace App\Services;

use App\Models\Product;
use App\Models\SerialBatch;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportService
{
    /** وارد کردن کالاها از CSV */
    public function importProducts(UploadedFile $file, int $tenantId, int $companyId, int $userId): array
    {
        $rows    = $this->parseCsv($file);
        $errors  = [];
        $created = 0;
        $updated = 0;

        DB::transaction(function () use ($rows, $tenantId, $companyId, $userId, &$errors, &$created, &$updated) {
            foreach ($rows as $i => $row) {
                $line = $i + 2;
                // ستون‌های اجباری
                if (empty($row['title'])) {
                    $errors[] = "ردیف $line: نام کالا الزامی است.";
                    continue;
                }

                try {
                    $product = Product::firstOrNew([
                        'tenant_id' => $tenantId,
                        'sku'       => $row['sku'] ?? null,
                    ]);

                    $isNew = !$product->exists;

                    $product->fill([
                        'tenant_id'  => $tenantId,
                        'company_id' => $companyId,
                        'title'      => trim($row['title']),
                        'sku'        => $row['sku'] ?? null,
                        'unit'       => $row['unit'] ?? null,
                        'sale_price' => $row['sale_price'] ?? 0,
                        'buy_price'  => $row['buy_price'] ?? 0,
                        'min_stock'  => $row['min_stock'] ?? 0,
                        'description'=> $row['description'] ?? null,
                        'created_by' => $userId,
                    ]);
                    $product->save();

                    $isNew ? $created++ : $updated++;
                } catch (\Throwable $e) {
                    $errors[] = "ردیف $line: " . $e->getMessage();
                    Log::error('ImportService@importProducts', ['row' => $row, 'error' => $e->getMessage()]);
                }
            }
        });

        return compact('created', 'updated', 'errors');
    }

    /** ─── دانلود قالب CSV ────────────────────────────────────────────────── */
    public static function productTemplate(): string
    {
        return "title,sku,unit,sale_price,buy_price,min_stock,description\n";
    }

    // ─── CSV Parser ───────────────────────────────────────────────────────────
    private function parseCsv(UploadedFile $file): array
    {
        $handle  = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle);
        if (!$headers) return [];

        $headers = array_map('trim', $headers);
        $rows    = [];

        while (($line = fgetcsv($handle)) !== false) {
            if (count($line) === count($headers)) {
                $rows[] = array_combine($headers, array_map('trim', $line));
            }
        }
        fclose($handle);
        return $rows;
    }
}
