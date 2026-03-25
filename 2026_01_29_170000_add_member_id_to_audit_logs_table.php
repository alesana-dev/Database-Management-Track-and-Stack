<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        if (! Schema::hasColumn('audit_logs', 'member_id')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                // No FK so logs survive permanent delete
                $table->unsignedBigInteger('member_id')->nullable()->after('chapter_id');
                $table->index(['chapter_id', 'member_id', 'action']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        if (Schema::hasColumn('audit_logs', 'member_id')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('audit_logs_chapter_id_member_id_action_index');
                $table->dropColumn('member_id');
            });
        }
    }
};
