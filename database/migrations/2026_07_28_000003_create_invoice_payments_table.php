<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─── پرداخت‌های فاکتور (واریز و تسویه) ───────────────────────────
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('company_id')->index();

            // فاکتور مرجع — یکی از دو فیلد پر می‌شود
            $table->unsignedBigInteger('sales_invoice_id')->nullable()->index();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable()->index();

            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();

            $table->date('payment_date');
            $table->decimal('amount', 18, 4);

            // روش پرداخت: cash | cheque | bank_transfer | card | other
            $table->string('payment_method', 50)->default('bank_transfer');
            $table->string('reference_number', 100)->nullable(); // شماره چک / شناسه تراکنش
            $table->date('cheque_date')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
