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

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // دسته بندی تنظیمات
            $table->string('group')
                ->default('general');

            // کلید تنظیمات
            $table->string('key');

            // نوع داده
            $table->enum('type', [
                'string',
                'integer',
                'float',
                'boolean',
                'json'
            ])->default('string');

            // مقدار
            $table->longText('value')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'organization_id',
                'key'
            ]);

            $table->index([
                'tenant_id',
                'organization_id'
            ]);

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