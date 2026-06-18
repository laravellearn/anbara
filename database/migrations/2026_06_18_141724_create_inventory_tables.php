<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // واحدهای اندازه‌گیری
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('title');         // کیلوگرم، متر، عدد
            $table->string('symbol', 20)->nullable(); // kg, m, pcs
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // دسته‌بندی کالا
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ویژگی‌های کالا
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');          // طول، رنگ، وزن
            $table->string('type')->default('text'); // text, number, select
            $table->json('options')->nullable();      // برای type=select
            $table->timestamps();
        });

        // کالاها
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name');
            $table->string('sku', 50)->nullable();
            $table->string('barcode')->nullable()->unique(); // بارکد اصلی
            $table->text('description')->nullable();
            $table->decimal('min_stock', 12, 2)->default(0);
            $table->decimal('max_stock', 12, 2)->default(0);
            $table->string('image')->nullable();
            $table->json('attributes')->nullable(); // ویژگی‌های دینامیک
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // بارکدهای اضافی
        Schema::create('product_barcodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('barcode')->unique();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // کالاهای جایگزین (رابطه چندبه‌چند)
        Schema::create('product_alternatives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('alternative_product_id')->constrained('products')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_id', 'alternative_product_id']);
        });

        // بسته‌بندی
        Schema::create('product_packagings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name');          // کارتن، پالت
            $table->decimal('quantity_per_unit', 10, 2)->default(1);
            $table->timestamps();
        });

        // انبارها
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->string('address')->nullable();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('capacity', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // موقعیت‌های انبار (قفسه، راهرو، طبقه)
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('warehouse_locations')->nullOnDelete();
            $table->string('code');
            $table->string('name')->nullable();
            $table->string('type')->default('shelf'); // aisle, rack, shelf, bin
            $table->decimal('capacity', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('product_packagings');
        Schema::dropIfExists('product_alternatives');
        Schema::dropIfExists('product_barcodes');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('units');
    }
};
