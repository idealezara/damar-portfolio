<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            ['Laravel', 'fa-brands fa-laravel'],
            ['PHP', 'fa-brands fa-php'],
            ['MySQL', 'fa-solid fa-database'],
            ['HTML', 'fa-brands fa-html5'],
            ['CSS', 'fa-brands fa-css3-alt'],
            ['JavaScript', 'fa-brands fa-js'],
        ];

        foreach ($skills as $i => [$name, $iconClass]) {
            Skill::firstOrCreate(['name' => $name], ['icon_class' => $iconClass, 'sort_order' => $i + 1]);
        }
    }
}
