<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ChapterController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $chapters = Chapter::withCount('members')
            ->when($user->isChapterAdmin(), function ($q) use ($user) {
                $q->where('id', $user->chapter_id);
            })
            ->orderBy('name')
            ->get();

        return view('chapters.index', compact('chapters'));
    }

    public function show(Chapter $chapter)
    {
        $user = Auth::user();

        if ($user->isChapterAdmin() && $chapter->id !== $user->chapter_id) {
            abort(403);
        }

        $chapter->load(['members' => function ($q) {
            $q->orderBy('last_name');
        }]);

        return view('chapters.show', compact('chapter'));
    }

    public function export(Chapter $chapter)
    {
        $user = Auth::user();

        if ($user->isChapterAdmin() && $chapter->id !== $user->chapter_id) {
            abort(403);
        }

        $members = $chapter->members()
            ->withCount('cases')
            ->orderBy('last_name')
            ->get();

        $pdf = Pdf::loadView('chapters.export', compact('chapter', 'members'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("chapter-{$chapter->code}-members.pdf");
    }

    public function updateFounders(Request $request)
    {
        $user = Auth::user();

        if (! $user->isChapterAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'history' => ['nullable', 'string'],
            'founders' => ['nullable', 'string'],
            'vicinity_area' => ['nullable', 'string', 'max:255'],
        ]);

        $chapter = Chapter::findOrFail($user->chapter_id);
        $chapter->update([
            'history' => $validated['history'] ?? null,
            'founders' => $validated['founders'] ?? null,
            'vicinity_area' => $validated['vicinity_area'] ?? null,
        ]);

        return redirect()
            ->route('chapters.profile.edit')
            ->with('success', 'Chapter profile updated.');
    }

    public function editProfile()
    {
        $user = Auth::user();

        if (! $user->isChapterAdmin()) {
            abort(403);
        }

        $chapter = Chapter::findOrFail($user->chapter_id);

        return view('chapters.profile', compact('chapter'));
    }
}
