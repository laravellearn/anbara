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
        Schema::create('assets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            // کد اموال
            $table->string('asset_code')->unique();

            // شماره سریال سازنده
            $table->string('serial_number')
                ->nullable();

            // شماره اموال سازمان
            $table->string('inventory_number')
                ->nullable();

            $table->date('purchase_date')
                ->nullable();

            $table->decimal('purchase_price', 18, 2)
                ->default(0);

            $table->date('warranty_until')
                ->nullable();

            $table->string('status')
                ->default('available');

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'organization_id'
            ]);

            $table->index('asset_code');

            $table->index('serial_number');
        });

        Schema::create('asset_assignments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();

            // تحویل دهنده
            $table->foreignId('assigned_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('assigned_at');

            $table->dateTime('expected_return_at')
                ->nullable();

            $table->dateTime('returned_at')
                ->nullable();

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('description')
                ->nullable();
            $table->string('status')
                ->default('assigned');

            $table->softDeletes();
            $table->timestamps();

            $table->index([
                'asset_id',
                'employee_id'
            ]);
        });

        Schema::create('asset_repairs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('repair_date');

            $table->string('repair_center')
                ->nullable();

            $table->decimal('cost', 18, 2)
                ->default(0);

            $table->string('status')
                ->default('pending');

            $table->date('completed_at')
                ->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('problem_description')
                ->nullable();

            $table->text('repair_description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('asset_id');
        });

        Schema::create('asset_scraps', function (Blueprint $table) {

            $table->id();

            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('scrap_date');

            $table->decimal('book_value', 18, 2)
                ->default(0);

            $table->decimal('scrap_value', 18, 2)
                ->default(0);

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reason');

            $table->text('description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('asset_id');
        });


        Schema::create('asset_histories', function (Blueprint $table) {

            $table->id();

            $table->foreignId('asset_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('action');

            $table->json('data')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index('asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
        Schema::dropIfExists('asset_scraps');
        Schema::dropIfExists('asset_repairs');
        Schema::dropIfExists('asset_assignments');
        Schema::dropIfExists('assets');
    }
};
