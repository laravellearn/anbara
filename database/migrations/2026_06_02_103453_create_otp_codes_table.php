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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('mobile', 20);
            $table->string('code');           // فقط ۶ رقم کافیه
            $table->timestamp('expires_at');
            $table->integer('attempts')->default(0);
            $table->boolean('is_used')->default(false);
            $table->ipAddress('ip')->nullable();
            $table->timestamps();

            $table->index(['mobile', 'code']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
