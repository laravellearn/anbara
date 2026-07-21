<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_documents', function (Blueprint $table) {
            $table->id();

            // چند-مستأجری
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');

            // شماره سند خودکار (مثلاً REC-1404-0001)
            $table->string('document_number', 30)->unique();

            // نوع سند
            $table->string('type', 30); // receipt | issue | transfer | adjustment | return_in | return_out

            // وضعیت گردش‌کار
            $table->string('status', 20)->default('draft'); // draft | pending | approved | rejected | cancelled

            // انبار اصلی (مبدأ برای انتقال)
            $table->foreignId('warehouse_id')->constrained('warehouses');

            // انبار مقصد (فقط برای انتقال)
            $table->foreignId('destination_warehouse_id')->nullable()->constrained('warehouses');

            // موقعیت پیش‌فرض (اختیاری)
            $table->foreignId('warehouse_location_id')->nullable()->constrained('warehouse_locations');

            // ارجاع به طرف خارجی (تأمین‌کننده / پرسنل / ...)
            $table->foreignId('contact_id')->nullable()->constrained('contacts');

            // سال مالی و مرکز هزینه
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');

            // تاریخ سند
            $table->date('document_date');

            // یادداشت و مرجع خارجی
            $table->string('reference_number', 100)->nullable(); // شماره فاکتور / PO / ...
            $table->text('description')->nullable();

            // ثبت‌کننده و تأییدکننده
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'type', 'status']);
            $table->index(['tenant_id', 'document_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_documents');
    }
};
