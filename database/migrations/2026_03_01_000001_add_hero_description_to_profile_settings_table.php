<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds an independent Hero description. SAFE for existing data:
 * - only ADDS a nullable column; nothing is dropped, renamed or reset;
 * - "biography" is only READ, never written (the UPDATE below sets hero_description only);
 * - only rows whose hero_description is NULL are filled, so re-running never overwrites an edited value;
 * - the starting Hero text is a copy of the biography that fits the 300-character limit WITHOUT
 *   cutting through a word (see fitToLimit). Short biographies are copied unchanged.
 *
 * Uses only plain PHP (mb_* functions), Schema and DB, which behave the same on Laravel 10, 11 and 12.
 */
return new class extends Migration
{
    private const MAX = 300;

    public function up(): void
    {
        if (! Schema::hasColumn('profile_settings', 'hero_description')) {
            Schema::table('profile_settings', function (Blueprint $table) {
                $table->string('hero_description', self::MAX)->nullable()->after('biography');
            });
        }

        foreach (DB::table('profile_settings')->whereNull('hero_description')->get(['id', 'biography']) as $row) {
            $hero = $this->fitToLimit((string) $row->biography, self::MAX);

            if ($hero === '') {
                continue; // nothing to copy: the Hero falls back to its built-in default sentence
            }

            DB::table('profile_settings')->where('id', $row->id)->update(['hero_description' => $hero]);
        }
    }

    /**
     * Shorten text to at most $max characters without cutting a word in half.
     *  1. Fits already: returned as is (only whitespace is tidied).
     *  2. Otherwise cut after the last complete sentence inside the limit (if that keeps at least 40 %).
     *  3. Otherwise cut at the last space and add an ellipsis (total length stays within $max).
     *  4. Only a single word longer than the limit is hard-cut (nothing better is possible).
     * The original text is never modified; this returns a new string.
     */
    public function fitToLimit(string $text, int $max = self::MAX): string
    {
        $collapsed = preg_replace('/\s+/u', ' ', $text);
        $text = trim($collapsed ?? $text);

        if ($text === '' || mb_strlen($text, 'UTF-8') <= $max) {
            return $text;
        }

        // 2. last complete sentence that fits
        $best = null;
        if (preg_match_all('/[.!?](?=\s|$)/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as [, $byteOffset]) {
                $length = mb_strlen(substr($text, 0, $byteOffset), 'UTF-8') + 1;

                if ($length > $max) {
                    break;
                }
                $best = $length;
            }
        }

        if ($best !== null && $best >= (int) floor($max * 0.4)) {
            return mb_substr($text, 0, $best, 'UTF-8');
        }

        // 3. last whole word, leaving one character for the ellipsis
        $limit = $max - 1;
        $cut = mb_substr($text, 0, $limit, 'UTF-8');

        if (! preg_match('/\s/u', mb_substr($text, $limit, 1, 'UTF-8'))) {
            $space = mb_strrpos($cut, ' ', 0, 'UTF-8');

            if ($space !== false && $space > 0) {
                $cut = mb_substr($cut, 0, $space, 'UTF-8');
            }
        }

        $cut = preg_replace('/[\s,;:\-–—]+$/u', '', $cut) ?? $cut;

        return $cut.'…';
    }

    public function down(): void
    {
        // Removes only the column added by this migration (not biography or any other column).
        if (Schema::hasColumn('profile_settings', 'hero_description')) {
            Schema::table('profile_settings', function (Blueprint $table) {
                $table->dropColumn('hero_description');
            });
        }
    }
};
