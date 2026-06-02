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
        Schema::create('purchase_invoices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('contact_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('invoice_number');

            $table->date('invoice_date');

            $table->decimal('total_amount', 18, 2)
                ->default(0);

            $table->decimal('discount_amount', 18, 2)
                ->default(0);

            $table->decimal('tax_amount', 18, 2)
                ->default(0);

            $table->decimal('final_amount', 18, 2)
                ->default(0);

            $table->text('description')
                ->nullable();

            $table->string('status')
                ->default('draft');

            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'tenant_id',
                'invoice_number'
            ]);
        });

        Schema::create('purchase_invoice_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('purchase_invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('quantity', 18, 4);

            $table->decimal('unit_price', 18, 2);

            $table->decimal('total_price', 18, 2);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoice_items');
        Schema::dropIfExists('purchase_invoices');
    }
};
