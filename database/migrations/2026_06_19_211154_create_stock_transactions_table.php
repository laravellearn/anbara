<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\InventoryTransactionType;
use App\Enums\InventoryTransactionStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measurement_unit_id')->nullable()->constrained()->nullOnDelete();

            // نوع تراکنش - مطابق با Enum App\Enums\InventoryTransactionType
            $table->string('type', 30);

            // وضعیت گردش‌کاری تراکنش - مطابق با Enum App\Enums\InventoryTransactionStatus
            $table->string('status', 20)->default(InventoryTransactionStatus::DRAFT->value);

            $table->decimal('quantity', 18, 4);
            $table->decimal('unit_price', 18, 4)->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('description')->nullable();

            // کاربری که این تراکنش را ثبت کرده است
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // ارتباط چندریختی (polymorphic) با سند مبدا
            // مثلاً فاکتور خرید، فاکتور فروش، حواله انتقال، رسید تنظیم انبار و ...
            // در فاز فعلی این دو ستون فقط رزرو می‌شوند؛ منطق اتصال در فاز ماژول فاکتور تکمیل خواهد شد.
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');

            $table->index(['tenant_id', 'company_id']);
            $table->index(['warehouse_id', 'product_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};