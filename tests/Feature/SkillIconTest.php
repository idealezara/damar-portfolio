<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SkillIconTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public static function validIcons(): array
    {
        return [
            ['fa-brands fa-php'], ['fa-brands fa-laravel'], ['fa-brands fa-js'], ['fa-brands fa-html5'],
            ['fa-brands fa-css3-alt'], ['fa-brands fa-git-alt'], ['fa-solid fa-database'], ['fas fa-code'],
        ];
    }

    public static function invalidIcons(): array
    {
        return [
            'script tag' => ['<script>alert(1)</script>'],
            'attribute break-out' => ['fa-solid fa-code" onmouseover="alert(1)'],
            'javascript url' => ['javascript:alert(1)'],
            'pro style' => ['fa-light fa-code'],
            'no icon name' => ['fa-solid'],
            'extra class' => ['fa-solid fa-code extra-class'],
            'bare name' => ['code'],
            'uppercase unicode' => ['fa-solid fa-cödé'],
        ];
    }

    #[DataProvider('validIcons')]
    public function test_valid_icon_classes_are_saved_and_rendered_publicly(string $class): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => $class])
            ->assertSessionHasNoErrors();

        $this->get('/')->assertSee('<i class="'.$class.'"', false);
    }

    #[DataProvider('invalidIcons')]
    public function test_invalid_icon_classes_are_rejected(string $class): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => $class])
            ->assertSessionHasErrors('icon_class');

        $this->assertDatabaseCount('skills', 0);
    }

    public function test_icon_class_is_required_and_normalised(): void
    {
        $admin = $this->actingAs($this->admin());

        $admin->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => ''])->assertSessionHasErrors('icon_class');
        $admin->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => '  FA-Brands   fa-PHP '])->assertSessionHasNoErrors();

        $this->assertSame('fa-brands fa-php', Skill::firstOrFail()->icon_class);
    }

    public function test_editing_the_icon_updates_the_public_icon(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => 'fa-brands fa-php']);
        $skill = Skill::firstOrFail();

        $this->actingAs($admin)->put(route('admin.skills.update', $skill), ['name' => 'Tool', 'icon_class' => 'fa-brands fa-laravel']);

        $this->get('/')->assertSee('<i class="fa-brands fa-laravel"', false)->assertDontSee('fa-brands fa-php');
    }

    public function test_category_and_level_are_optional_and_validated(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.skills.store'), [
            'name' => 'Laravel', 'icon_class' => 'fa-brands fa-laravel', 'category' => 'Backend', 'level' => 'Advanced',
        ])->assertSessionHasNoErrors();
        $this->get('/')->assertSee('Backend')->assertSee('Advanced');

        $this->actingAs($admin)->post(route('admin.skills.store'), [
            'name' => 'X', 'icon_class' => 'fa-solid fa-code', 'level' => 'Wizard',
        ])->assertSessionHasErrors('level');
    }

    public function test_skill_name_is_escaped_on_the_public_site(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.skills.store'), ['name' => '<script>alert(1)</script>', 'icon_class' => 'fa-solid fa-code']);

        $this->get('/')->assertDontSee('<script>alert(1)</script>', false)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }

    public function test_tampered_database_value_falls_back_to_a_safe_icon(): void
    {
        // Simulates a bad value written outside the validated form.
        Skill::create(['name' => 'Bad', 'icon_class' => 'x" onmouseover="alert(1)']);

        $this->get('/')
            ->assertDontSee('onmouseover', false)
            ->assertSee('<i class="'.Skill::FALLBACK_ICON.'"', false);
    }

    public function test_guests_cannot_manage_skills(): void
    {
        $this->post(route('admin.skills.store'), ['name' => 'Tool', 'icon_class' => 'fa-solid fa-code'])
            ->assertRedirect(route('admin.login'));
        $this->get(route('admin.skills.create'))->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('skills', 0);
    }
}
