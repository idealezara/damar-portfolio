<?php

namespace Database\Seeders;

use App\Models\ProfileSetting;
use Illuminate\Database\Seeder;

class ProfileSettingSeeder extends Seeder
{
    public function run(): void
    {
        ProfileSetting::firstOrCreate(['id' => 1], ProfileSetting::defaults());
    }
}
