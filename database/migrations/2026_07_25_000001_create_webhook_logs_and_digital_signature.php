<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('direction', ['incoming', 'outgoing'])->default('incoming');
            $table->string('type', 50);
            $table->text('url')->nullable();
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('status', 30)->default('received');
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
            $table->index('status');
        });

        // ─── اضافه کردن ستون‌های digital_signature به warehouse_documents ───
        Schema::table('warehouse_documents', function (Blueprint $table) {
            $table->uuid('verify_uuid')->nullable()->unique()->after('id');
            $table->string('digital_signature', 64)->nullable()->after('rejection_reason');
            $table->timestamp('signed_at')->nullable()->after('digital_signature');
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete()->after('signed_at');
        });

        // ─── اضافه کردن verify_uuid به purchase_invoices ─────────────────────
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->uuid('verify_uuid')->nullable()->unique()->after('id');
            $table->string('digital_signature', 64)->nullable()->after('cancellation_reason');
            $table->timestamp('signed_at')->nullable()->after('digital_signature');
            $table->foreignId('signed_by')->nullable()->constrained('users')->nullOnDelete()->after('signed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
        Schema::table('warehouse_documents', function (Blueprint $table) {
            $table->dropColumn(['verify_uuid', 'digital_signature', 'signed_at', 'signed_by']);
        });
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn(['verify_uuid', 'digital_signature', 'signed_at', 'signed_by']);
        });
    }
};
