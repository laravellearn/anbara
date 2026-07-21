<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('invoice_number', 50)->unique();
            $table->string('status', 30)->default('draft');  // draft|confirmed|partially_paid|paid|cancelled
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();   // contacts.id
            $table->unsignedBigInteger('warehouse_id')->nullable();  // انبار مرتبط
            $table->unsignedBigInteger('warehouse_document_id')->nullable();  // حواله خروج
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->unsignedBigInteger('cost_center_id')->nullable();
            $table->decimal('subtotal', 18, 4)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 18, 4)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(9);
            $table->decimal('tax_amount', 18, 4)->default(0);
            $table->decimal('total_amount', 18, 4)->default(0);
            $table->decimal('paid_amount', 18, 4)->default(0);
            $table->text('description')->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id')->index();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('measurement_unit_id')->nullable();
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 4);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 18, 4)->default(0);
            $table->decimal('total_price', 18, 4);
            $table->string('description', 255)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
    }
};
