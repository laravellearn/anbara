<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── جدول دارایی‌های ثابت ───────────────────────────────────────────
        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');

            $table->string('asset_code')->comment('کد دارایی');
            $table->string('title');
            $table->string('serial_number')->nullable()->comment('شماره سریال');
            $table->text('description')->nullable();

            // دسته‌بندی
            $table->string('category')->nullable()->comment('دسته: ساختمان، خودرو، تجهیزات، ...');
            $table->string('location')->nullable()->comment('محل فیزیکی');

            // اطلاعات مالی
            $table->decimal('purchase_price', 18, 2)->default(0)->comment('قیمت خرید');
            $table->decimal('current_value',  18, 2)->default(0)->comment('ارزش جاری');
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry')->nullable()->comment('تاریخ انقضای ضمانت');

            // وضعیت: active, assigned, under_maintenance, retired, scrapped
            $table->string('status')->default('active');

            // تصویر
            $table->string('image')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status']);
        });

        // ─── جدول تخصیص دارایی به پرسنل ────────────────────────────────────
        Schema::create('fixed_asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('fixed_asset_id');
            $table->unsignedBigInteger('employee_id')->nullable()->comment('کارمند گیرنده');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->date('assigned_at');
            $table->date('returned_at')->nullable();
            $table->string('status')->default('active')->comment('active, returned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['fixed_asset_id', 'status']);
        });

        // ─── جدول تعمیر و نگهداری ───────────────────────────────────────────
        Schema::create('fixed_asset_maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('fixed_asset_id');
            $table->date('maintenance_date');
            $table->string('type')->default('repair')->comment('repair, service, inspection');
            $table->text('description')->nullable();
            $table->decimal('cost', 18, 2)->default(0);
            $table->string('performed_by')->nullable()->comment('نام فرد/شرکت انجام‌دهنده');
            $table->date('next_maintenance_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['fixed_asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_maintenances');
        Schema::dropIfExists('fixed_asset_assignments');
        Schema::dropIfExists('fixed_assets');
    }
};
