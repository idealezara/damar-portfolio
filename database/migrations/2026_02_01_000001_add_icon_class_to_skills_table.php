<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds Font Awesome icon_class, category and level to skills.
 * Existing rows are KEPT: icon_class is filled from the skill name or the old icon key.
 * The old "icon" column is left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->string('icon_class', 80)->nullable()->after('name');
            $table->string('category', 40)->nullable()->after('icon_class');
            $table->string('level', 20)->nullable()->after('category');
        });

        $byName = [
            'laravel' => 'fa-brands fa-laravel',
            'php' => 'fa-brands fa-php',
            'mysql' => 'fa-solid fa-database',
            'html' => 'fa-brands fa-html5',
            'html5' => 'fa-brands fa-html5',
            'css' => 'fa-brands fa-css3-alt',
            'css3' => 'fa-brands fa-css3-alt',
            'javascript' => 'fa-brands fa-js',
            'git' => 'fa-brands fa-git-alt',
        ];

        $byOldKey = [
            'code' => 'fa-solid fa-code',
            'terminal' => 'fa-solid fa-terminal',
            'database' => 'fa-solid fa-database',
            'server' => 'fa-solid fa-server',
            'layers' => 'fa-solid fa-layer-group',
            'layout' => 'fa-solid fa-table-columns',
            'globe' => 'fa-solid fa-globe',
        ];

        foreach (DB::table('skills')->get() as $skill) {
            $class = $byName[strtolower(trim($skill->name))]
                ?? $byOldKey[$skill->icon ?? '']
                ?? 'fa-solid fa-code';

            DB::table('skills')->where('id', $skill->id)->update(['icon_class' => $class]);
        }
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['icon_class', 'category', 'level']);
        });
    }
};
