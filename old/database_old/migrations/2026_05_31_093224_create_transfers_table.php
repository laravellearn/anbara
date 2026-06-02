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
        Schema::create('transfers', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            // انبار مبدا
            $table->foreignId('from_warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            // انبار مقصد
            $table->foreignId('to_warehouse_id')
                ->constrained('warehouses')
                ->cascadeOnDelete();

            // ثبت کننده درخواست
            $table->foreignId('requested_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // تایید کننده
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // رد کننده
            $table->foreignId('rejected_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('transfer_number')
                ->unique();

            $table->date('request_date');

            $table->date('approved_at')
                ->nullable();

            $table->date('received_at')
                ->nullable();

            $table->string('status')
                ->default('draft');
            $table->text('description')
                ->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index([
                'tenant_id',
                'organization_id'
            ]);
        });

        Schema::create('transfer_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('transfer_id')
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

            $table->string('serial_number')
                ->nullable();

            $table->string('batch_number')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->timestamps();
        });


        Schema::create('transfer_approvals', function (Blueprint $table) {

            $table->id();

            $table->foreignId('transfer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('level');

            $table->string('status');

            $table->text('comment')
                ->nullable();

            $table->timestamp('action_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_approvals');
        Schema::dropIfExists('transfer_items');
        Schema::dropIfExists('transfers');
    }
};
