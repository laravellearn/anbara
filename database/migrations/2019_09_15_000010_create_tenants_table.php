<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
    
            // فیلدهای ضروری برای stancl/tenancy
            $table->string('name');                    // نام تننت
            $table->string('domain')->nullable()->unique();
            $table->json('data')->nullable();          // خیلی مهم برای پکیج

            // فیلدهای سفارشی خودت
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('theme_color')->nullable();
            
            $table->timestamp('trial_ends_at')->nullable();
            
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};