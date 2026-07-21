<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // سال مالی — برای گزارش‌های مالی دوره‌ای
            $table->foreignId('fiscal_year_id')
                ->nullable()
                ->after('company_id')
                ->constrained('fiscal_years')
                ->nullOnDelete();

            // مرکز هزینه — برای تخصیص هزینه‌ها
            $table->foreignId('cost_center_id')
                ->nullable()
                ->after('fiscal_year_id')
                ->constrained('cost_centers')
                ->nullOnDelete();

            $table->index('fiscal_year_id');
            $table->index('cost_center_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropForeign(['cost_center_id']);
            $table->dropIndex(['fiscal_year_id']);
            $table->dropIndex(['cost_center_id']);
            $table->dropColumn(['fiscal_year_id', 'cost_center_id']);
        });
    }
};
