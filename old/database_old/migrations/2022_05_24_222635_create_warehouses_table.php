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
        Schema::create('warehouses', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('code', 50);

            $table->string('title');

            $table->text('description')->nullable();

            $table->text('address')->nullable();

            $table->boolean('allow_negative_stock')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'code'
            ]);

            $table->unique([
                'tenant_id',
                'title'
            ]);

            $table->index('tenant_id');

            $table->index([
                'tenant_id',
                'organization_id'
            ]);
        });

        Schema::create('warehouse_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_default')
                ->default(false);

            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'warehouse_id',
                'user_id'
            ]);
        });

        Schema::create('warehouse_locations', function (Blueprint $table) {

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

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('warehouse_locations')
                ->nullOnDelete();

            $table->string('code', 50);

            $table->string('title');

            $table->text('description')->nullable();

            $table->integer('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'warehouse_id',
                'code'
            ]);

            $table->unique([
                'warehouse_id',
                'title'
            ]);

            $table->index([
                'tenant_id',
                'warehouse_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
        Schema::dropIfExists('warehouse_user');
        Schema::dropIfExists('warehouses');
    }
};
