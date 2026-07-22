<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── جدول پیوست‌ها ────────────────────────────────────────────────────
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->morphs('attachable'); // attachable_type + attachable_id
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['attachable_type', 'attachable_id']);
        });

        // ─── جدول تاریخچه قیمت ───────────────────────────────────────────────
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->decimal('unit_price', 18, 4);
            $table->string('currency', 10)->default('IRR');
            $table->date('price_date');
            $table->string('source', 50)->default('manual'); // manual | purchase_order | invoice
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id', 'product_id']);
            $table->index(['product_id', 'supplier_id', 'price_date']);
        });

        // ─── اضافه کردن ستون‌های تأیید/رد به warehouse_documents ─────────────
        if (!Schema::hasColumn('warehouse_documents', 'rejected_by')) {
            Schema::table('warehouse_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('rejected_by')->nullable()->after('approved_by');
                $table->timestamp('rejected_at')->nullable()->after('approved_at');
                $table->text('rejection_reason')->nullable()->after('rejected_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
        Schema::dropIfExists('attachments');

        Schema::table('warehouse_documents', function (Blueprint $table) {
            $table->dropColumn(['rejected_by', 'rejected_at', 'rejection_reason']);
        });
    }
};
