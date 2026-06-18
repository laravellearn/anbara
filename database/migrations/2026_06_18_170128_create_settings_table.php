<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('group')->default('general');
            $table->string('key');
            $table->enum('type', ['string', 'integer', 'float', 'boolean', 'json'])->default('string');
            $table->longText('value')->nullable();
            $table->timestamps();
            $table->unique(['tenant_id', 'company_id', 'key']);
            $table->index(['tenant_id', 'company_id']);
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
