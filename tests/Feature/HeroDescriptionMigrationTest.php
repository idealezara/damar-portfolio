<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests the hero_description backfill. Runs on the TEST database only
 * (phpunit.xml: in-memory SQLite by default), never on your main database.
 */
class HeroDescriptionMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function migration(): object
    {
        return require database_path('migrations/2026_03_01_000001_add_hero_description_to_profile_settings_table.php');
    }

    private function words(int $count): string
    {
        return implode(' ', array_map(fn ($i) => 'word'.$i, range(1, $count)));
    }

    private function insertProfile(string $biography, ?string $hero = null): int
    {
        return DB::table('profile_settings')->insertGetId([
            'full_name' => 'Test User', 'title' => 'Dev', 'biography' => $biography, 'hero_description' => $hero,
            'email' => 't@example.com', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_short_text_is_returned_unchanged(): void
    {
        $text = 'I build modern and dynamic web applications using Laravel and PHP.';

        $this->assertSame($text, $this->migration()->fitToLimit($text));
    }

    public function test_long_text_is_cut_at_a_word_boundary_within_the_limit(): void
    {
        $text = $this->words(120); // about 1000 characters, no punctuation
        $result = $this->migration()->fitToLimit($text);

        $this->assertLessThanOrEqual(300, mb_strlen($result));
        $this->assertStringEndsWith('…', $result);

        $prefix = mb_substr($result, 0, -1);
        $this->assertStringStartsWith($prefix, $text);
        $this->assertSame(' ', mb_substr($text, mb_strlen($prefix), 1), 'cut must fall on a space, not inside a word');
    }

    public function test_prefers_a_complete_sentence_when_one_fits(): void
    {
        $sentence = 'This is a reasonably long sentence about building web applications with care.';
        $text = trim(str_repeat($sentence.' ', 8));
        $result = $this->migration()->fitToLimit($text);

        $this->assertLessThanOrEqual(300, mb_strlen($result));
        $this->assertGreaterThanOrEqual(120, mb_strlen($result));
        $this->assertStringEndsWith('.', $result);
        $this->assertStringNotContainsString('…', $result);
        $this->assertStringStartsWith($result, $text);
    }

    public function test_multibyte_text_is_not_corrupted(): void
    {
        $text = implode(' ', array_fill(0, 120, 'résumé ünïcode'));
        $result = $this->migration()->fitToLimit($text);

        $this->assertLessThanOrEqual(300, mb_strlen($result));
        $this->assertTrue(mb_check_encoding($result, 'UTF-8'));
        $this->assertStringEndsWith('…', $result);
    }

    public function test_a_single_overlong_word_is_hard_cut_to_the_limit(): void
    {
        $result = $this->migration()->fitToLimit(str_repeat('a', 400));

        $this->assertSame(300, mb_strlen($result));
        $this->assertStringEndsWith('…', $result);
    }

    public function test_backfill_never_changes_the_biography_or_an_existing_hero_description(): void
    {
        $long = $this->words(120);
        $short = 'A short biography.';
        $a = $this->insertProfile($long);
        $b = $this->insertProfile($short);
        $c = $this->insertProfile('Biography that must stay', 'Hero text edited by the owner');

        $migration = $this->migration();
        $migration->up(); // idempotent: the column exists, so this runs the backfill only
        $migration->up(); // running twice must change nothing further

        $rowA = DB::table('profile_settings')->find($a);
        $rowB = DB::table('profile_settings')->find($b);
        $rowC = DB::table('profile_settings')->find($c);

        $this->assertSame($long, $rowA->biography);
        $this->assertSame($short, $rowB->biography);
        $this->assertSame('Biography that must stay', $rowC->biography);

        $this->assertNotNull($rowA->hero_description);
        $this->assertLessThanOrEqual(300, mb_strlen($rowA->hero_description));
        $this->assertSame($short, $rowB->hero_description);
        $this->assertSame('Hero text edited by the owner', $rowC->hero_description);
    }
}
