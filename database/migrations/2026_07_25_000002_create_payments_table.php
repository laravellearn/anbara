<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->unsignedBigInteger('subscription_id')->nullable()->index();
            $table->string('gateway', 30)->default('zarinpal');
            $table->string('authority', 100)->nullable()->unique();   // شناسه Zarinpal
            $table->string('ref_id', 50)->nullable();                  // کد رهگیری موفق
            $table->unsignedBigInteger('amount');                      // مبلغ به ریال
            $table->string('description', 255)->nullable();
            $table->string('status', 20)->default('pending');          // pending | paid | failed | canceled
            $table->string('payer_mobile', 20)->nullable();
            $table->json('gateway_response')->nullable();               // پاسخ کامل gateway
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
