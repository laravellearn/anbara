<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            // ظرفیت موقعیت انبار (واحد: تعداد یا وزن بر اساس تنظیمات)
            $table->decimal('capacity', 18, 4)
                ->nullable()
                ->after('sort_order')
                ->comment('حداکثر ظرفیت این موقعیت انبار');
        });
    }

    public function down(): void
    {
        Schema::table('warehouse_locations', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
};
