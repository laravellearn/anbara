<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_document_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_document_id')->constrained('warehouse_documents')->cascadeOnDelete();

            // کالا
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units');

            // موقعیت ردیف (override از سند)
            $table->foreignId('warehouse_location_id')->nullable()->constrained('warehouse_locations');

            // مقادیر
            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 4)->nullable();

            // سریال / بچ / انقضا
            $table->string('serial_number', 100)->nullable();
            $table->string('batch_number', 100)->nullable();
            $table->date('expiry_date')->nullable();

            // یادداشت ردیف
            $table->text('notes')->nullable();

            // مقدار تأیید شده (برای رسید جزئی)
            $table->decimal('received_quantity', 18, 4)->nullable();

            // ارجاع به stock_transaction ایجادشده پس از تأیید
            $table->foreignId('stock_transaction_id')->nullable()->constrained('stock_transactions')->nullOnDelete();

            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('warehouse_document_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_document_items');
    }
};
