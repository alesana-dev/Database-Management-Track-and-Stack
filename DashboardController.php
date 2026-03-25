<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ✅ Super Admin: system-wide analytics
        if ($user->isSuperAdmin()) {
            $stats = [
                'chapters'   => Chapter::count(),
                'users'      => User::count(),
                'members'    => Member::count(),

                'active'     => Member::where('status', 'active')->count(),
                'inactive'   => Member::where('status', 'inactive')->count(),
                'suspended'  => Member::where('status', 'suspended')->count(),
                'expelled'   => Member::where('status', 'expelled')->count(),
            ];

            $topChapters = Chapter::withCount('members')
                ->orderByDesc('members_count')
                ->take(10)
                ->get();

            return view('dashboard', [
                'title'       => 'TRIPAC Admin Dashboard',
                'stats'       => $stats,
                'topChapters' => $topChapters,
            ]);
        }

        // ✅ Chapter Admin: chapter-only analytics
        if ($user->isChapterAdmin()) {
            $chapterId = $user->chapter_id;

            $stats = [
                'members'   => Member::where('chapter_id', $chapterId)->count(),

                'active'    => Member::where('chapter_id', $chapterId)->where('status', 'active')->count(),
                'inactive'  => Member::where('chapter_id', $chapterId)->where('status', 'inactive')->count(),
                'suspended' => Member::where('chapter_id', $chapterId)->where('status', 'suspended')->count(),
                'expelled'  => Member::where('chapter_id', $chapterId)->where('status', 'expelled')->count(),
            ];

            return view('dashboard', [
                'title' => 'Chapter Admin Dashboard',
                'stats' => $stats,
            ]);
        }

        // 🚫 No other roles should ever access the system
        abort(403, 'Unauthorized');
    }
}
