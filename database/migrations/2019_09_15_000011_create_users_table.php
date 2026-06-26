<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('name');

            $table->string('code')->nullable();
            $table->string('logo')->nullable();

            $table->string('national_id')
                ->nullable();

            $table->string('economic_code')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();
            $table->index('tenant_id');

            $table->index('parent_id');
        });

        Schema::create('organizational_units', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('organizational_units')
                ->nullOnDelete();

            $table->string('name');

            $table->string('code')
                ->nullable();
            $table->text('description')->nullable();

            $table->foreignId('manager_user_id')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->softDeletes();
            $table->index('tenant_id');
            $table->index('company_id');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')
                ->nullable();
            $table->string('mobile', 11)->unique();
            $table->timestamp('mobile_verified_at')
                ->nullable();
            $table->string('national_code', 20)
                ->nullable();
            $table->string('password');
            $table->string('avatar')->default('img/avatars/avatar.png');

            $table->boolean('is_active')->default(false);
            $table->string('last_ip', 45)->nullable(); // پشتیبانی IPv4 و IPv6
            $table->timestamp('last_login_at')
                ->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->index('tenant_id');
        });

        Schema::create('company_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_default')
                ->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->unique([
                'company_id',
                'user_id'
            ]);
        });

        Schema::create('organizational_unit_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('organizational_unit_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('edited_by')->nullable()->constrained('users')->nullOnDelete()->after('updated_at');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete()->after('edited_by');
            $table->unique([
                'organizational_unit_id',
                'user_id'
            ]);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('organizational_unit_user');
        Schema::dropIfExists('company_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('organizational_units');
        Schema::dropIfExists('companies');
    }
};
