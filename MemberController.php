<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Chapter;
use App\Models\CaseHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;

class MemberController extends Controller
{
    /**
     * 📋 List members
     */
    public function index()
    {
        $user = Auth::user();

        $members = Member::query()
            ->when($user->isSuperAdmin(), function ($q) {
                $q->with('chapter');
            })
            ->when($user->isChapterAdmin(), function ($q) use ($user) {
                $q->where('chapter_id', $user->chapter_id);
            })
            ->orderBy('last_name')
            ->get();

        return view('members.index', compact('members'));
    }

    /**
     * 👁 View member profile + case history
     */
    public function show(Member $member)
    {
        $this->authorizeChapter($member);

        $member->load([
            'cases' => function ($q) {
                $q->latest();
            },
            'cases.filedBy',
        ]);

        return view('members.show', compact('member'));
    }

    /**
     * ➕ Create member form
     */
    public function create()
    {
        $user = Auth::user();

        $chapters = $user->isSuperAdmin()
            ? Chapter::orderBy('name')->get()
            : Chapter::where('id', $user->chapter_id)->get();

        return view('members.create', compact('chapters'));
    }

    /**
     * 💾 Store member
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'alias'         => 'nullable|string|max:255',
            'batch_name'    => 'nullable|string|max:255',
            'gt_initiated'  => 'nullable|string|max:255',
            'mww_initiated' => 'nullable|string|max:255',
            'status'        => 'required|in:active,inactive,suspended,expelled',
            'notes'         => 'nullable|string',
            'chapter_id'    => 'nullable|exists:chapters,id',
        ]);

        // 🔒 Chapter Admin locked to own chapter
        if ($user->isChapterAdmin()) {
            $validated['chapter_id'] = $user->chapter_id;
        }

        // Chapter Admins can expel too

        $member = Member::create($validated);

        AuditLogger::member(
            action: 'created',
            member: $member,
            before: null,
            after: $member->toArray(),
            note: 'Member created',
            request: $request
        );

        return redirect()
            ->route('members.index')
            ->with('success', 'Member added successfully');
    }

    /**
     * ✏️ Edit form
     */
    public function edit(Member $member)
    {
        $this->authorizeChapter($member);

        return view('members.edit', compact('member'));
    }

    /**
     * 🔄 Update member
     */
    public function update(Request $request, Member $member)
    {
        $this->authorizeChapter($member);

        $before = $member->toArray();
        $beforeStatus = $member->status;

        $validated = $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'alias'         => 'nullable|string|max:255',
            'batch_name'    => 'nullable|string|max:255',
            'gt_initiated'  => 'nullable|string|max:255',
            'mww_initiated' => 'nullable|string|max:255',
            'status'        => 'required|in:active,inactive,suspended,expelled',
            'notes'         => 'nullable|string',
        ]);

        // Chapter Admins can expel too

        $member->update($validated);

        // ✅ Auto-log status changes (CaseHistory)
        if ($beforeStatus !== $member->status) {

            $severity = match ($member->status) {
                'inactive'  => 'minor',
                'suspended' => 'major',
                'expelled'  => 'critical',
                default     => 'minor',
            };

            \App\Models\CaseHistory::create([
                'member_id'   => $member->id,
                'user_id'     => Auth::id(),
                'action'      => $member->status,
                'severity'    => $severity,
                'description' => "Status changed from {$beforeStatus} to {$member->status}",
            ]);
        }


        AuditLogger::member(
            action: 'updated',
            member: $member,
            before: $before,
            after: $member->fresh()->toArray(),
            note: 'Member updated',
            request: $request
        );

        return redirect()
            ->route('members.index')
            ->with('success', 'Member updated successfully');
    }

    /**
     * 🗑 PERMANENT DELETE (Super Admin only)
     */
    public function destroy(Request $request, Member $member)
    {
        if (! Auth::user()->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete member records');
        }

        $before = $member->toArray();

        AuditLogger::member(
            action: 'deleted',
            member: $member,
            before: $before,
            after: null,
            note: 'Permanent delete (correction / wrong input)',
            request: $request
        );

        $member->delete();

        return redirect()
            ->route('members.index')
            ->with('success', 'Member permanently deleted');
    }

    /**
     * 🔐 Chapter isolation
     */
    private function authorizeChapter(Member $member): void
    {
        $user = Auth::user();

        if ($user->isChapterAdmin() && $member->chapter_id !== $user->chapter_id) {
            abort(403, 'Unauthorized chapter access');
        }
    }
}
