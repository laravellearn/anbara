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
        Schema::create('measurement_units', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('measurement_units')
                ->nullOnDelete();

            $table->string('title');

            $table->string('symbol', 20)
                ->nullable();

            /*
     * ضریب تبدیل به والد
     *
     * مثال:
     *
     * عدد => null
     * بسته => 12
     * کارتن => 24
     *
     * یعنی:
     * 1 بسته = 12 عدد
     * 1 کارتن = 24 بسته
     */
            $table->decimal('conversion_factor', 18, 6)
                ->default(1);

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'title'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_units');
    }
};
