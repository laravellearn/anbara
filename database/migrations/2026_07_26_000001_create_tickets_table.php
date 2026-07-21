<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('ticket_number', 30)->unique();
            // open → in_progress → waiting_user → resolved → closed
            $table->string('status', 30)->default('open');
            $table->string('priority', 20)->default('normal'); // low|normal|high|urgent
            $table->string('category', 50)->default('general'); // general|billing|technical|warehouse
            $table->string('subject', 255);
            $table->text('description');
            $table->foreignId('user_id')->constrained('users');           // submitter
            $table->foreignId('assigned_to')->nullable()->constrained('users'); // super-admin agent
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('body');
            $table->boolean('is_staff')->default(false); // پاسخ کارشناس یا کاربر
            $table->string('attachment_path', 500)->nullable();
            $table->timestamps();
            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
    }
};
