<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── سند برگشت (فروش و خرید) ──────────────────────────────────────
        Schema::create('return_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('return_number', 50)->unique();

            // نوع برگشت: sales | purchase
            $table->string('type', 20)->default('sales');
            // وضعیت: draft | confirmed | cancelled
            $table->string('status', 30)->default('draft');

            // فاکتور اصلی مرجع (اختیاری — ممکن است مستقل ثبت شود)
            $table->unsignedBigInteger('sales_invoice_id')->nullable()->index();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable()->index();

            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers')->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();

            // طرف حساب (مشتری یا تأمین‌کننده)
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            $table->date('return_date');
            $table->string('reason', 255)->nullable();  // دلیل برگشت

            $table->decimal('subtotal',   18, 4)->default(0);
            $table->decimal('discount_amount', 18, 4)->default(0);
            $table->decimal('tax_amount', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);

            $table->text('notes')->nullable();
            $table->string('reference_number', 100)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'status']);
            $table->index(['tenant_id', 'return_date']);
        });

        // ─── اقلام برگشت ──────────────────────────────────────────────────
        Schema::create('return_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_invoice_id')->constrained('return_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units')->nullOnDelete();
            $table->decimal('quantity',   18, 4);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('line_total', 18, 4)->default(0);
            $table->string('serial_batch', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('return_invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_invoice_items');
        Schema::dropIfExists('return_invoices');
    }
};
