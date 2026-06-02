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
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('transaction_number')->unique();

            $table->string('type');
            $table->dateTime('transaction_date');

            $table->string('reference_type')->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('status')
                ->default('approved');
            $table->text('description')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index([
                'tenant_id',
                'warehouse_id'
            ]);

            $table->index([
                'reference_type',
                'reference_id'
            ]);
        });


        Schema::create('inventory_transaction_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('inventory_transaction_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('warehouse_location_id')
                ->nullable()
                ->constrained('warehouse_locations')
                ->nullOnDelete();

            $table->decimal('quantity', 18, 4);

            $table->foreignId('measurement_unit_id')
                ->constrained('measurement_units')
                ->cascadeOnDelete();

            $table->decimal('unit_price', 18, 2)
                ->default(0);

            $table->decimal('total_price', 18, 2)
                ->default(0);

            $table->text('description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transaction_items');
        Schema::dropIfExists('inventory_transactions');
    }
};
