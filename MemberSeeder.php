<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Member;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $chapters = Chapter::all();

        foreach ($chapters as $chapter) {

            // Create 400 members per chapter
            for ($i = 1; $i <= 400; $i++) {
                Member::create([
                    'chapter_id'    => $chapter->id,
                    'first_name'    => $faker->firstName,
                    'alias'         => $faker->optional()->userName,
                    'last_name'     => $faker->lastName,
                    'batch_name'    => 'Batch ' . rand(1, 30),
                    't_birth'       => $faker->optional()->date(),
                    'gt_initiated'  => $faker->name,
                    'mww_initiated' => $faker->name,
                    'status'        => $faker->randomElement([
                        'active',
                        'inactive',
                        'suspended',
                        'expelled',
                    ]),
                    'notes'         => $faker->optional()->sentence(),
                ]);
            }
        }
    }
}
