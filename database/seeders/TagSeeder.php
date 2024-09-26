<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Food'],
            ['name' => 'Finance'],
            ['name' => 'Fashion'],
            ['name' => 'Sports'],
            ['name' => 'Music'],
            ['name' => 'Environment'],
        ];

        // Insert tags into the database
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
