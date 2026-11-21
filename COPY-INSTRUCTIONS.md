# Copy instructions (Laragon)

Project folder: `C:\laragon\www\PROJECT` (use your real folder name).

## 0. Back up the database first (recommended)
Laragon > Database > HeidiSQL > right-click your database > Export, or:
`mysqldump -u root portfolio > backup.sql`

## Laravel version
This package targets Laravel 11 or 12 and uses nothing version-specific in the migration. Confirm yours with:
```
php artisan --version
```
(If it prints 10.x or lower, tell me before applying.)

## 1. Copy the files
From inside `portfolio-revision-2`:
```
robocopy . C:\laragon\www\PROJECT /E /XF CHANGELOG.md COPY-INSTRUCTIONS.md
```
(robocopy codes 0-7 mean success.)

| File | Status | Destination |
|---|---|---|
| database/migrations/2026_03_01_000001_add_hero_description_to_profile_settings_table.php | NEW | same path |
| tests/Feature/ProfileContentTest.php | NEW | same path |
| tests/Feature/HeroDescriptionMigrationTest.php | NEW | same path |
| app/Models/ProfileSetting.php | MODIFIED (overwrites) | same path |
| app/Http/Requests/ProfileRequest.php | MODIFIED (overwrites) | same path |
| resources/views/admin/profile/edit.blade.php | MODIFIED (overwrites) | same path |
| resources/views/public/sections/hero.blade.php | MODIFIED (overwrites) | same path |
| resources/views/layouts/public.blade.php | MODIFIED (overwrites) | same path |
| tests/Feature/PortfolioTest.php | MODIFIED (overwrites; includes revision 1 changes) | same path |

Revision 1 must already be applied (the package builds on it). If you edited any of these files yourself, merge instead.

## 2. Migration (never use migrate:fresh)
```
php artisan migrate
```
Only the new migration runs. It adds `profile_settings.hero_description` and copies each existing biography into it
once, so your landing page looks unchanged. No data is deleted. No `.env` or npm changes.
Undo (only if needed): `php artisan migrate:rollback --step=1`

## 3. After copying
**Before `php artisan test`: confirm the tests will not touch your main database.** The tests use RefreshDatabase,
which wipes whatever database the tests are configured for. In a default Laravel project that is in-memory SQLite.
Check:
```
findstr DB_ phpunit.xml
```
You should see `DB_CONNECTION` = `sqlite` and `DB_DATABASE` = `:memory:` (and the PHP `pdo_sqlite` extension enabled).
If you see `mysql` or your real database name, do NOT run the tests; tell me first.
```
php artisan optimize:clear
php artisan test
```
No `npm run build` is needed (no CSS or JS changed).

## 4. Check the data survived
```
php artisan tinker
>>> App\Models\ProfileSetting::first()->only(['full_name','title','hero_description','biography','motivation','email'])
```
`biography` must be exactly your old text. `hero_description` starts as a copy of it: identical when your biography is
300 characters or less; otherwise shortened at a sentence or word boundary (never mid-word).

## 5. Manual check
1. Admin > Profile: you now see "Hero description" and "About biography" as separate fields.
2. Change only Hero description, save: only the intro under your name on the landing page changes.
3. Change only About biography: only the paragraph in About changes.
4. Change only Motivation: only the big About heading changes.
5. Check at phone width (browser dev tools, 375 px).
