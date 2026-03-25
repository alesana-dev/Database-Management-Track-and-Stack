<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Member;

class MemberPolicy
{
    /**
     * View any members
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isChapterAdmin();
    }

    /**
     * View a specific member
     */
    public function view(User $user, Member $member): bool
    {
        return $user->isSuperAdmin()
            || $member->chapter_id === $user->chapter_id;
    }

    /**
     * Create members
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isChapterAdmin();
    }

    /**
     * Update member
     */
    public function update(User $user, Member $member): bool
    {
        return $user->isSuperAdmin()
            || $member->chapter_id === $user->chapter_id;
    }

    /**
     * Delete member
     */
    public function delete(User $user, Member $member): bool
    {
        return $user->isSuperAdmin()
            || $member->chapter_id === $user->chapter_id;
    }
}
