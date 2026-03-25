<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::where('role', User::ROLE_CHAPTER_ADMIN)
            ->with('chapter')
            ->orderBy('username')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'username'   => ['required', 'string', 'max:255', 'unique:users,username'],
            'password'   => ['required', 'confirmed', 'min:8'],
            'chapter_name' => ['required', 'string', 'max:255'],
            'chapter_sector' => ['required', 'string', 'max:50', 'unique:chapters,code'],
            'chapter_history' => ['nullable', 'string'],
            'chapter_vicinity_area' => ['nullable', 'string', 'max:255'],
            'status'     => ['required', 'in:active,inactive'],
        ]);

        $chapter = Chapter::create([
            'name'   => $validated['chapter_name'],
            'code'   => $validated['chapter_sector'],
            'history' => $validated['chapter_history'] ?? null,
            'vicinity_area' => $validated['chapter_vicinity_area'] ?? null,
            'status' => 'active',
        ]);

        User::create([
            'username'    => $validated['username'],
            'password'    => Hash::make($validated['password']),
            'role'        => User::ROLE_CHAPTER_ADMIN,
            'chapter_id'  => $chapter->id,
            'first_login' => true,
            'status'      => $validated['status'],
        ]);

        return redirect()
            ->route('users.create')
            ->with('success', 'Chapter Admin account created.');
    }

    public function editPassword(User $user): View
    {
        $this->ensureChapterAdmin($user);

        return view('users.edit-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        $this->ensureChapterAdmin($user);

        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Password updated.');
    }

    private function ensureChapterAdmin(User $user): void
    {
        if ($user->role !== User::ROLE_CHAPTER_ADMIN) {
            abort(404);
        }
    }
}
