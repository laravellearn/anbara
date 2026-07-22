<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── ۱. انتقال بین انبارها ─────────────────────────────────────────
        Schema::create('transfer_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->string('transfer_number')->unique();
            $table->enum('status', ['draft','confirmed','in_transit','completed','cancelled'])->default('draft');
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');
            $table->date('transfer_date');
            $table->date('expected_arrival_date')->nullable();
            $table->date('actual_arrival_date')->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('confirmed_by')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses');
            $table->index(['tenant_id','company_id','status']);
        });

        Schema::create('transfer_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('measurement_unit_id')->nullable();
            $table->decimal('quantity_requested', 14, 4);
            $table->decimal('quantity_transferred', 14, 4)->default(0);
            $table->decimal('unit_price', 14, 4)->nullable();
            $table->string('batch_number')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('transfer_order_id')->references('id')->on('transfer_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });

        // ─── ۲. انبارگردانی ────────────────────────────────────────────────
        Schema::create('physical_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->string('inventory_number')->unique();
            $table->enum('status', ['draft','counting','completed','adjusted','cancelled'])->default('draft');
            $table->unsignedBigInteger('warehouse_id');
            $table->date('inventory_date');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('adjusted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->index(['tenant_id','company_id','status']);
        });

        Schema::create('physical_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('physical_inventory_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('measurement_unit_id')->nullable();
            $table->decimal('system_quantity', 14, 4)->default(0);   // موجودی سیستمی
            $table->decimal('counted_quantity', 14, 4)->nullable();   // موجودی شمارش‌شده
            $table->decimal('difference', 14, 4)->default(0);         // مغایرت
            $table->decimal('unit_price', 14, 4)->nullable();
            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('adjustment_transaction_id')->nullable(); // تراکنش تعدیل
            $table->timestamps();

            $table->foreign('physical_inventory_id')->references('id')->on('physical_inventories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
        });

        // ─── ۳. قوانین نقطه سفارش (Reorder) ──────────────────────────────
        Schema::create('reorder_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable(); // null = همه انبارها
            $table->unsignedBigInteger('preferred_supplier_id')->nullable();
            $table->decimal('reorder_point', 14, 4);        // نقطه سفارش
            $table->decimal('reorder_quantity', 14, 4);     // مقدار سفارش پیشنهادی
            $table->decimal('safety_stock', 14, 4)->default(0);
            $table->unsignedInteger('lead_time_days')->default(7); // لید تایم (روز)
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_suggested_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['tenant_id','company_id','product_id','warehouse_id']);
        });

        // ─── ۴. قراردادهای تأمین‌کننده ────────────────────────────────────
        Schema::create('supplier_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('fiscal_year_id')->nullable();
            $table->string('contract_number')->unique();
            $table->enum('status', ['draft','active','expired','terminated'])->default('draft');
            $table->unsignedBigInteger('supplier_id');
            $table->string('title');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('credit_limit', 16, 2)->default(0);         // سقف اعتبار
            $table->decimal('payment_terms_days', 5, 0)->default(30);   // شرایط پرداخت (روز)
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();                      // فایل ضمیمه
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('contacts');
            $table->index(['tenant_id','company_id','status']);
        });

        // ─── ۵. کاتالوگ قیمت ──────────────────────────────────────────────
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->string('name');
            $table->enum('type', ['retail','wholesale','vip','special'])->default('retail');
            $table->text('description')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id','company_id','is_active']);
        });

        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('price_list_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('min_quantity', 14, 4)->default(1);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->timestamps();

            $table->foreign('price_list_id')->references('id')->on('price_lists')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products');
            $table->unique(['price_list_id','product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
        Schema::dropIfExists('supplier_contracts');
        Schema::dropIfExists('reorder_rules');
        Schema::dropIfExists('physical_inventory_items');
        Schema::dropIfExists('physical_inventories');
        Schema::dropIfExists('transfer_order_items');
        Schema::dropIfExists('transfer_orders');
    }
};
