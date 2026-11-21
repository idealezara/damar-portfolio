<?php

namespace Tests\Feature;

use App\Models\ProfileSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Hero description, About biography and About heading (motivation) are independent. */
class ProfileContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');

        ProfileSetting::current()->update([
            'hero_description' => 'Hero text original',
            'biography' => 'About biography original',
            'motivation' => 'stay curious',
        ]);
    }

    private function saveProfile(array $overrides = [])
    {
        $current = ProfileSetting::current();
        $admin = User::factory()->create(['is_admin' => true]);

        return $this->actingAs($admin)->put(route('admin.profile.update'), array_merge([
            'full_name' => $current->full_name,
            'title' => $current->title,
            'hero_description' => $current->hero_description,
            'biography' => $current->biography,
            'motivation' => $current->motivation,
            'email' => $current->email,
            'github_url' => $current->github_url,
            'instagram_url' => $current->instagram_url,
            'availability_enabled' => '1',
            'availability_text' => $current->availability_text,
        ], $overrides));
    }

    /** Inner text of the first element with the given class on the home page. */
    private function section(string $tag, string $class): string
    {
        $this->app->forgetScopedInstances(); // fresh profile per request, as in production
        preg_match('/<'.$tag.' class="'.$class.'">(.*?)<\/'.$tag.'>/s', $this->get('/')->getContent(), $m);

        return trim($m[1] ?? '');
    }

    public function test_hero_and_about_render_different_fields(): void
    {
        $this->assertSame('Hero text original', $this->section('p', 'hero__intro'));
        $this->assertSame('About biography original', $this->section('p', 'about__bio'));
        $this->assertSame('Stay curious', $this->section('h2', 'about__heading'));
    }

    public function test_updating_hero_description_only_changes_the_hero(): void
    {
        $this->saveProfile(['hero_description' => 'Hero text updated'])->assertSessionHasNoErrors();

        $this->assertSame('Hero text updated', $this->section('p', 'hero__intro'));
        $this->assertSame('About biography original', $this->section('p', 'about__bio'));
        $this->assertSame('Stay curious', $this->section('h2', 'about__heading'));
    }

    public function test_updating_biography_only_changes_the_about_section(): void
    {
        $this->saveProfile(['biography' => 'About biography updated'])->assertSessionHasNoErrors();

        $this->assertSame('Hero text original', $this->section('p', 'hero__intro'));
        $this->assertSame('About biography updated', $this->section('p', 'about__bio'));
        $this->assertSame('Stay curious', $this->section('h2', 'about__heading'));
    }

    public function test_updating_motivation_only_changes_the_about_heading(): void
    {
        $this->saveProfile(['motivation' => 'keep going'])->assertSessionHasNoErrors();

        $this->assertSame('Hero text original', $this->section('p', 'hero__intro'));
        $this->assertSame('About biography original', $this->section('p', 'about__bio'));
        $this->assertSame('Keep going', $this->section('h2', 'about__heading'));
    }

    public function test_other_profile_data_stays_intact_after_updates(): void
    {
        $before = ProfileSetting::current()->only(['full_name', 'title', 'email', 'github_url', 'instagram_url', 'availability_text']);

        $this->saveProfile(['hero_description' => 'Changed hero']);
        $this->saveProfile(['biography' => 'Changed biography']);

        $this->assertSame($before, ProfileSetting::current()->only(array_keys($before)));
    }

    public function test_both_fields_are_required_and_hero_is_limited_to_300_characters(): void
    {
        $this->saveProfile(['hero_description' => ''])->assertSessionHasErrors('hero_description');
        $this->saveProfile(['hero_description' => str_repeat('a', 301)])->assertSessionHasErrors('hero_description');
        $this->saveProfile(['biography' => ''])->assertSessionHasErrors('biography');
        $this->saveProfile(['hero_description' => str_repeat('a', 300)])->assertSessionHasNoErrors();
    }

    public function test_empty_hero_description_falls_back_to_a_default_and_never_to_the_biography(): void
    {
        ProfileSetting::current()->update(['hero_description' => null, 'biography' => 'Only about text']);

        $this->assertSame(ProfileSetting::DEFAULT_HERO_DESCRIPTION, $this->section('p', 'hero__intro'));
        $this->assertSame('Only about text', $this->section('p', 'about__bio'));
    }

    public function test_profile_form_has_separate_fields(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get(route('admin.profile.edit'))
            ->assertOk()
            ->assertSee('name="hero_description"', false)
            ->assertSee('name="biography"', false)
            ->assertSee('Hero description')
            ->assertSee('About biography');
    }

    public function test_guests_cannot_update_the_profile(): void
    {
        $this->put(route('admin.profile.update'), ['hero_description' => 'x'])->assertRedirect(route('admin.login'));
        $this->assertSame('Hero text original', ProfileSetting::current()->hero_description);
    }
}
