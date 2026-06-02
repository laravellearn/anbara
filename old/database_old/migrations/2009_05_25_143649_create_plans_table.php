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

        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->string('currency', 10)->default('IRT');
            $table->integer('duration_days');
            $table->json('limits')->nullable();
            $table->json('features')->nullable();
            // {
            //     "max_products": 500,
            //     "max_users": 5,
            //     "max_warehouses": 2,
            //     "max_invoices_per_month": 100,
            //     "storage_mb": 500,
            //     "api_access": false
            // }

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
