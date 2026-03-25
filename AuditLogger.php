<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function member(
        string $action,
        ?Member $member,
        ?array $before,
        ?array $after,
        ?string $note = null,
        ?Request $request = null
    ): void {
        $user = Auth::user();

        AuditLog::create([
            'user_id'    => $user?->id,
            'chapter_id' => $member?->chapter_id ?? $user?->chapter_id,
            'member_id'  => $member?->id,
            'action'     => $action,
            'entity'     => 'Member',
            'model'      => 'Member',
            'before'     => $before,
            'after'      => $after,
            'note'       => $note,
            'ip'         => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
