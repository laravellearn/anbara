<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('plans', function (Blueprint $table) {

            $table->id();

            $table->string('name');
            $table->string('slug')->unique();            // برای آدرس‌دهی (basic, pro, ...)
            $table->string('code')->unique()->nullable(); // کد سیستمی (در صورت نیاز جدا)
            $table->text('description')->nullable();

            $table->decimal('monthly_price', 12, 2)->default(0); // قیمت (ماهانه یا بر اساس duration)
            $table->decimal('yearly_price', 12, 2)->default(0); // قیمت (ماهانه یا بر اساس duration)
            $table->string('currency', 10)->default('IRT');
            $table->unsignedInteger('duration_days')->nullable(); // null = نامحدود
            $table->json('limits')->nullable();   // محدودیت‌ها (max_users, max_products و غیره)
            $table->json('features')->nullable(); // قابلیت‌های فعال (core_inventory, api و...)

            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
