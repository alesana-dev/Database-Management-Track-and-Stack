<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ChapterAdminSeeder extends Seeder
{
    public function run(): void
    {
        $chapter = Chapter::firstOrFail();

        User::create([
            'username'    => 'chapteradmin',
            'password'    => Hash::make('password'),
            'role'        => User::ROLE_CHAPTER_ADMIN,
            'chapter_id'  => $chapter->id,
            'first_login' => false,
            'status'      => 'active',
        ]);
    }
}
