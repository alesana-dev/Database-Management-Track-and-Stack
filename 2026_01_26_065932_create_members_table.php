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
    Schema::create('members', function (Blueprint $table) {
        $table->id();

        // Ownership
        $table->foreignId('chapter_id')
              ->constrained()
              ->cascadeOnDelete();

        // Identity
        $table->string('first_name');
        $table->string('alias')->nullable();
        $table->string('last_name');

        // Fraternity details
        $table->string('batch_name')->nullable();
        $table->date('t_birth')->nullable();

        // Initiators (NAMES, not dates)
        $table->string('gt_initiated')->nullable();
        $table->string('mww_initiated')->nullable();

        // Status
        $table->enum('status', [
            'active',
            'inactive',
            'suspended',
            'expelled'
        ])->default('active');

        // Notes
        $table->text('notes')->nullable();

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
