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

        Schema::table('audit_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('audit_logs', 'before')) {
                $table->json('before')->nullable()->after('entity');
            }
            if (! Schema::hasColumn('audit_logs', 'after')) {
                $table->json('after')->nullable()->after('before');
            }
            if (! Schema::hasColumn('audit_logs', 'note')) {
                $table->text('note')->nullable()->after('after');
            }
            if (! Schema::hasColumn('audit_logs', 'ip')) {
                $table->string('ip')->nullable()->after('note');
            }
            if (! Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::table('audit_logs', function (Blueprint $table) {
            if (Schema::hasColumn('audit_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
            if (Schema::hasColumn('audit_logs', 'ip')) {
                $table->dropColumn('ip');
            }
            if (Schema::hasColumn('audit_logs', 'note')) {
                $table->dropColumn('note');
            }
            if (Schema::hasColumn('audit_logs', 'after')) {
                $table->dropColumn('after');
            }
            if (Schema::hasColumn('audit_logs', 'before')) {
                $table->dropColumn('before');
            }
        });
    }
};
