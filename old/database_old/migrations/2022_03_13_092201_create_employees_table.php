<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('unit_id')
                ->nullable()
                ->constrained('units')
                ->nullOnDelete();

            // اگر کارمند حساب کاربری سیستم داشته باشد
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('employee_code', 50)
                ->nullable();

            $table->string('name');

            $table->string('national_code', 20)
                ->nullable();

            $table->string('mobile', 20)
                ->nullable();

            $table->string('phone', 20)
                ->nullable();

            $table->string('position')
                ->nullable();

            $table->date('employment_date')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'employee_code'
            ]);

            $table->index([
                'tenant_id',
                'organization_id'
            ]);

            $table->index('unit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
