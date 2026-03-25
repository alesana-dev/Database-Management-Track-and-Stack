<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('case_histories', function (Blueprint $table) {
            $table->enum('severity', ['minor', 'major', 'critical'])
                ->default('minor')
                ->after('action');
        });
    }

    public function down(): void
    {
        Schema::table('case_histories', function (Blueprint $table) {
            $table->dropColumn('severity');
        });
    }
};
