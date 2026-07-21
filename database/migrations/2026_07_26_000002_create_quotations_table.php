<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── پیش‌فاکتورها ────────────────────────────────────────────────────
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('quotation_number')->unique();
            $table->enum('status', ['draft','sent','accepted','rejected','expired','converted'])->default('draft');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(9);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('total_amount', 18, 2)->default(0);
            $table->unsignedBigInteger('sales_invoice_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('customer_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->nullOnDelete();
        });

        // ─── اقلام پیش‌فاکتور ────────────────────────────────────────────────
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('measurement_unit_id')->nullable();
            $table->decimal('quantity', 18, 3)->default(1);
            $table->decimal('unit_price', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('total_price', 18, 2)->default(0);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('quotations')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
