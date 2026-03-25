<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;

class ChapterSeeder extends Seeder
{
    public function run(): void
    {
        Chapter::create([
            'name'   => 'Alpha Chapter',
            'code'   => 'ALPHA',
            'status' => 'active',
        ]);

        Chapter::create([
            'name'   => 'Beta Chapter',
            'code'   => 'BETA',
            'status' => 'active',
        ]);

        Chapter::create([
            'name'   => 'Gamma Chapter',
            'code'   => 'GAMMA',
            'status' => 'inactive',
        ]);
    }
}
