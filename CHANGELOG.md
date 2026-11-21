# Changelog - separate Hero description and About biography

## Root cause
Both `public/sections/hero.blade.php` (line 14) and `public/sections/about.blade.php` (line 6) printed
`$profile->biography`, and the page meta description used it too. The profile table and form had no separate
field for the Hero text.

## Field map after this change
| Where it shows | Field (column) | Admin label |
|---|---|---|
| Hero: professional title | `title` (existing; kept under this name, not renamed) | Professional title |
| Hero: intro text | `hero_description` (NEW, max 300) | Hero description |
| About: large heading | `motivation` (existing) | Motivation (About heading) |
| About: paragraph | `biography` (existing) | About biography |

## Changes
- NEW migration `2026_03_01_000001_add_hero_description_to_profile_settings_table.php`: only adds a nullable
  column. Nothing is dropped, renamed or reset. Existing rows get `hero_description` = a copy of their current
  biography so the site looks the same right after migrating; from then on the two fields are edited independently.
  `biography` is only read, never written. Safe to run twice (checks if the column exists; only rows where
  `hero_description` is NULL are filled).
  **Length handling (revised):** the first version used `Str::limit(..., 300, '')`, which can cut through a word. It is
  replaced by `fitToLimit()` inside the migration, using only PHP `mb_*` functions plus Schema and DB (no
  Laravel-version-specific helper): text up to 300 characters is copied unchanged; longer text is cut after the last
  complete sentence that fits (if that keeps at least 120 characters), otherwise at the last space with an ellipsis,
  always within 300 characters. Only a single word longer than 300 characters is hard-cut. UTF-8 text is handled safely.
- `ProfileSetting` model: `hero_description` fillable, default value, and `hero_text` accessor. If the Hero
  description is ever empty, the Hero shows a fixed default sentence and never falls back to the biography.
- `ProfileRequest`: `hero_description` required, max 300. `biography` rules unchanged (required, max 1000).
- Admin Profile form: separate "Hero description" and "About biography" fields, each with a hint saying where it
  appears; the Motivation and Professional title fields also say where they appear.
- `hero.blade.php`: prints `hero_text`. `about.blade.php` is unchanged (it already printed `biography`).
- `layouts/public.blade.php`: the page meta description now uses the Hero text.
- Tests: new `ProfileContentTest.php` and `HeroDescriptionMigrationTest.php` (limiter and backfill); `PortfolioTest.php` updated because the profile form now requires
  `hero_description`.
- ProfileController, routes, CSS, JS and all other features are untouched.

## Testing status (honest)
EXECUTED here:
- Static check of the templates: Hero reads `hero_text`, About reads `biography` and `motivation`, meta uses Hero text.
- Browser layout test (Chromium, real CSS/JS, markup mirrored from the Blade views) with a 244-character Hero
  description and a different, longer About biography at 320, 375, 414, 768, 1024, 1366 and 1440 px: 7/7 passed,
  no horizontal overflow, the two sections show different text. Desktop screenshot checked by eye.

- Logic of the revised length limiter, via a line-by-line Python port (not the PHP itself): 7/7 cases passed (short text
  unchanged, word-boundary cut, sentence preference, UTF-8 text, single overlong word, a 321-character sample cut to 300
  at a word boundary).
- No migration was run against any database.

NOT executed (no PHP or MySQL in my environment): the migration itself, the model, validation, the Blade templates and
the PHPUnit tests (`ProfileContentTest` covers: hero-only update, biography-only update, motivation-only update,
other profile data unchanged, validation limits, empty-hero fallback, form fields, guest blocked). Please run
`php artisan test` and send me any error text. The migration backfill has not been run against real data;
check it with the commands in COPY-INSTRUCTIONS.md.
