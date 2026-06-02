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
        Schema::create('permissions', function (Blueprint $table) {

            $table->id();

            $table->string('name')->unique();
            // warehouse.create

            $table->string('title');
            // ایجاد انبار

            $table->text('description')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();

            $table->timestamps();
        });

        Schema::create('permission_user', function (Blueprint $table) {

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'permission_id',
                'user_id'
            ]);
        });

        Schema::create('roles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

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

        Schema::create('permission_role', function (Blueprint $table) {

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'permission_id',
                'role_id'
            ]);
        });


        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(['role_id', 'user_id']);
        });

        Schema::create('organization_user_role', function (Blueprint $table) {

            $table->id();

            $table->foreignId('organization_user_id')
                ->constrained('organization_user')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'organization_user_id',
                'role_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_user_role');
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
