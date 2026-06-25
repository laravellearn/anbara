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
            $table->string('group')->nullable();
            $table->string('title');
            // ایجاد انبار

            $table->boolean('is_active')
                ->default(true);

            $table->softDeletes();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->timestamps();
        });

        Schema::create('permission_user', function (Blueprint $table) {

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->index('tenant_id');

            $table->unique([
                'permission_id',
                'user_id'
            ]);
        });

        Schema::create('roles', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();

            // کد یکتا برای استفاده در برنامه
            $table->string('code');

            // عنوان نمایشی
            $table->string('title');

            $table->text('description')
                ->nullable();

            // نقش های سیستمی قابل حذف نباشند
            $table->boolean('is_system')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->unique([
                'tenant_id',
                'code'
            ]);

            $table->unique([
                'tenant_id',
                'title'
            ]);

            $table->index('tenant_id');
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

        Schema::create('company_user_role', function (Blueprint $table) {

            $table->id();

            $table->foreignId('company_user_id')
                ->constrained('company_user')
                ->cascadeOnDelete();

            $table->foreignId('role_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->unique([
                'company_user_id',
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
        Schema::dropIfExists('company_user_role');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permissions');
    }
};
