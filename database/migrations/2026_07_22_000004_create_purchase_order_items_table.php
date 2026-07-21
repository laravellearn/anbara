<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();

            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units');

            // مقادیر سفارش
            $table->decimal('quantity_ordered', 18, 4);
            $table->decimal('unit_price', 18, 4)->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);

            // دریافت
            $table->decimal('quantity_received', 18, 4)->default(0);

            // مشخصات فیزیکی
            $table->string('description', 255)->nullable();
            $table->date('expected_delivery_date')->nullable();

            // ارجاع به سند انبار پس از دریافت
            $table->foreignId('warehouse_document_id')->nullable()->constrained('warehouse_documents')->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('purchase_order_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
