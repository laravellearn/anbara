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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('subscription_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('gateway');                     // zarinpal, mellat, payir و...
            $table->string('transaction_id')->nullable();   // کد پیگیری درگاه (Authority در زرین‌پال)
            $table->string('ref_id')->nullable();           // شماره ارجاع بانکی (در صورت موفقیت)
            $table->decimal('amount', 12, 0);              // مبلغ پرداختی (تومان)
            $table->string('status')
                ->default('pending');
            $table->text('description')->nullable();        // توضیح اضافه
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
