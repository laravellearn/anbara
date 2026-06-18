<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            
            $table->unique(['tenant_id', 'code']);
            $table->unique(['tenant_id', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
    }
};