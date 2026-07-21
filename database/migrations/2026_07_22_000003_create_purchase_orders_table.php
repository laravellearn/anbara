<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();

            // چند-مستأجری
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');

            // شماره سفارش خودکار (PO-1404-00001)
            $table->string('po_number', 30)->unique();

            // گردش‌کار: draft → confirmed → sent → partial_received → received → closed | cancelled
            $table->string('status', 30)->default('draft');

            // تأمین‌کننده
            $table->foreignId('supplier_id')->nullable()->constrained('contacts');

            // انبار دریافت پیش‌فرض
            $table->foreignId('warehouse_id')->constrained('warehouses');

            // مالی
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');

            // تاریخ‌ها
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();

            // شرایط مالی
            $table->string('currency', 10)->default('IRR');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('tax_percent', 5, 2)->default(0);
            $table->decimal('shipping_cost', 18, 4)->default(0);

            // یادداشت و مرجع
            $table->string('reference_number', 100)->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();

            // کاربران چرخه تأیید
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status']);
            $table->index(['tenant_id', 'order_date']);
            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
