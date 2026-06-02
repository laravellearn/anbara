<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('brand_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('measurement_unit_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('sku')
                ->nullable();

            $table->string('barcode')
                ->nullable();

            $table->string('title');

            $table->string('model')
                ->nullable();

            $table->string('part_number')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->decimal('minimum_stock', 18, 4)
                ->default(0);

            $table->decimal('maximum_stock', 18, 4)
                ->nullable();

            $table->boolean('is_asset')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'sku'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
