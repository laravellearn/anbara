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
        Schema::create('sales_invoices', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('contact_id')
                ->constrained('contacts')
                ->cascadeOnDelete();

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

            $table->string('status')
                ->default('draft');
            $table->text('description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index([
                'tenant_id',
                'organization_id'
            ]);
        });


        Schema::create('sales_invoice_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('sales_invoice_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('quantity', 18, 4);

            $table->decimal('unit_price', 18, 2);

            $table->decimal('discount_amount', 18, 2)
                ->default(0);

            $table->decimal('tax_amount', 18, 2)
                ->default(0);

            $table->decimal('total_amount', 18, 2);

            $table->text('description')
                ->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoice');
    }
};
