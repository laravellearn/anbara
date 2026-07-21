<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');

            $table->string('ir_number', 30)->unique();
            // draft → submitted → approved → issued → rejected → cancelled
            $table->string('status', 30)->default('draft');

            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('approver_id')->nullable()->constrained('users');
            $table->foreignId('warehouse_id')->constrained('warehouses'); // انبار مبدأ
            $table->foreignId('organizational_unit_id')->nullable()->constrained('organizational_units');
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years');
            $table->foreignId('cost_center_id')->nullable()->constrained('cost_centers');
            // سند حواله انبار که بعد از تأیید ایجاد می‌شود
            $table->foreignId('warehouse_document_id')->nullable()->constrained('warehouse_documents')->nullOnDelete();

            $table->date('request_date');
            $table->date('required_by_date')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->text('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('issued_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'status']);
            $table->index(['tenant_id', 'request_date']);
        });

        Schema::create('item_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_request_id')->constrained('item_requests')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('measurement_unit_id')->nullable()->constrained('measurement_units');
            $table->decimal('quantity_requested', 18, 4);
            $table->decimal('quantity_issued', 18, 4)->default(0);
            $table->string('description', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index('item_request_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_request_items');
        Schema::dropIfExists('item_requests');
    }
};
