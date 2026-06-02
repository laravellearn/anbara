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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('organizations')
                ->nullOnDelete();

            $table->string('title');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'tenant_id',
                'title'
            ]);

            $table->index([
                'tenant_id',
                'parent_id'
            ]);
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
            $table->string('avatar')->default('/img/avatars/avatar.png');
            $table->boolean('is_active')->default(false);
            $table->string('last_ip',19)->nullable(); //192.168.210.100
            $table->timestamp('last_login_at')
                ->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->unique([
                'tenant_id',
                'mobile'
            ]);

            $table->unique([
                'tenant_id',
                'email'
            ]);

            $table->index('tenant_id');
        });

        Schema::create('organization_user', function (Blueprint $table) {

            $table->id();

            $table->foreignId('organization_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamp('joined_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'organization_id',
                'user_id'
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
        Schema::dropIfExists('organization_user');
        Schema::dropIfExists('users');
        Schema::dropIfExists('organizations');
    }
};
