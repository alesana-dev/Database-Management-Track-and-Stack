<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SchemaAudit extends Command
{
    protected $signature = 'schema:audit';
    protected $description = 'Check required database columns for core tables.';

    public function handle(): int
    {
        $expected = [
            'users' => [
                'id',
                'username',
                'password',
                'role',
                'chapter_id',
                'first_login',
                'status',
                'remember_token',
                'created_at',
                'updated_at',
            ],
            'chapters' => [
                'id',
                'name',
                'code',
                'history',
                'founders',
                'vicinity_area',
                'status',
                'created_at',
                'updated_at',
            ],
            'members' => [
                'id',
                'chapter_id',
                'first_name',
                'alias',
                'last_name',
                'batch_name',
                't_birth',
                'gt_initiated',
                'mww_initiated',
                'status',
                'notes',
                'deleted_at',
                'created_at',
                'updated_at',
            ],
            'audit_logs' => [
                'id',
                'user_id',
                'chapter_id',
                'member_id',
                'action',
                'entity',
                'model',
                'before',
                'after',
                'note',
                'ip',
                'user_agent',
                'created_at',
                'updated_at',
            ],
            'case_histories' => [
                'id',
                'member_id',
                'user_id',
                'action',
                'description',
                'severity',
                'created_at',
                'updated_at',
            ],
        ];

        $hasIssues = false;

        foreach ($expected as $table => $columns) {
            if (! Schema::hasTable($table)) {
                $this->error("Missing table: {$table}");
                $hasIssues = true;
                continue;
            }

            $missing = [];
            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    $missing[] = $column;
                }
            }

            if (! empty($missing)) {
                $this->error("{$table}: missing columns -> " . implode(', ', $missing));
                $hasIssues = true;
            } else {
                $this->info("{$table}: OK");
            }
        }

        if ($hasIssues) {
            $this->warn('Schema audit found issues. Review migrations and run them.');
            return self::FAILURE;
        }

        $this->info('Schema audit passed.');
        return self::SUCCESS;
    }
}
