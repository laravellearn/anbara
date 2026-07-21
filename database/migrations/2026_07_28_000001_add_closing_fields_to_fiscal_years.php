<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── فیلدهای بستن سال مالی ────────────────────────────────────────
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('is_closed');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete()->after('closed_at');
        });
    }

    public function down(): void
    {
        Schema::table('fiscal_years', function (Blueprint $table) {
            $table->dropForeign(['closed_by']);
            $table->dropColumn(['closed_at', 'closed_by']);
        });
    }
};
