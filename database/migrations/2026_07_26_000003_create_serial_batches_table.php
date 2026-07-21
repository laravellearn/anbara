<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('serial_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('warehouse_document_id')->nullable(); // سند انبار مرتبط
            $table->enum('tracking_type', ['serial', 'batch'])->default('batch');
            $table->string('serial_number', 100)->nullable()->index();
            $table->string('batch_number', 100)->nullable()->index();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->decimal('quantity', 18, 3)->default(1);
            $table->enum('status', ['in_stock', 'issued', 'returned', 'scrapped'])->default('in_stock')->index();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_batches');
    }
};
