<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('plans', function (Blueprint $table) {

            $table->id();
        
            $table->string('name');
        
            $table->string('code')
                ->unique();
        
            $table->decimal('monthly_price',12,2)
                ->default(0);
        
            $table->decimal('yearly_price',12,2)
                ->default(0);
        
            $table->text('description')
                ->nullable();
        
            $table->boolean('is_active')
                ->default(true);
        
            $table->timestamps();
        });
    
        Schema::create('plan_features', function (Blueprint $table) {

            $table->id();
        
            $table->foreignId('plan_id')
                ->constrained()
                ->cascadeOnDelete();
        
            $table->string('feature_key');
        
            $table->json('feature_value');
        
            $table->timestamps();
        
            $table->unique([
                'plan_id',
                'feature_key'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
        Schema::dropIfExists('plans');
    }
};
