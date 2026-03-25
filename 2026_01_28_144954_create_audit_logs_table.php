<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Admin who did it (keep FK ok)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Keep chapter FK (optional but safe)
            $table->foreignId('chapter_id')->nullable()->constrained()->nullOnDelete();

            // IMPORTANT: no FK for member_id (so logs survive permanent delete)
            $table->unsignedBigInteger('member_id')->nullable();

            $table->string('action');          // created, updated, status_changed, deleted
            $table->string('entity')->default('Member');
            $table->string('model')->default('Member');

            $table->json('before')->nullable(); // snapshot BEFORE
            $table->json('after')->nullable();  // snapshot AFTER
            $table->text('note')->nullable();

            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['chapter_id', 'member_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
