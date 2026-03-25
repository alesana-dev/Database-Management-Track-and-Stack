<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('chapters')) {
            return;
        }

        Schema::table('chapters', function (Blueprint $table) {
            if (! Schema::hasColumn('chapters', 'history')) {
                $table->text('history')->nullable()->after('code');
            }
            if (! Schema::hasColumn('chapters', 'founders')) {
                $table->text('founders')->nullable()->after('history');
            }
            if (! Schema::hasColumn('chapters', 'vicinity_area')) {
                $table->string('vicinity_area')->nullable()->after('founders');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('chapters')) {
            return;
        }

        Schema::table('chapters', function (Blueprint $table) {
            if (Schema::hasColumn('chapters', 'vicinity_area')) {
                $table->dropColumn('vicinity_area');
            }
            if (Schema::hasColumn('chapters', 'founders')) {
                $table->dropColumn('founders');
            }
            if (Schema::hasColumn('chapters', 'history')) {
                $table->dropColumn('history');
            }
        });
    }
};
