<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Requires: PHP gd + pdo_sqlite extensions (phpunit.xml uses in-memory SQLite).
 * Run with: php artisan test
 */
class PortfolioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function projectPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test Project',
            'category' => 'Web App',
            'short_description' => 'Short text',
            'description' => 'Long text',
            'technologies' => 'Laravel, PHP',
            'project_url' => 'https://example.com',
            'github_url' => '',
            'is_published' => '1',
        ], $overrides);
    }

    public function test_homepage_and_project_detail_load(): void
    {
        $project = Project::create([
            'title' => 'Visible', 'slug' => 'visible', 'short_description' => 's',
            'description' => 'd', 'is_published' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('Visible')->assertSee('Damar');
        $this->get(route('projects.show', $project->slug))->assertOk()->assertSee('Visible');
    }

    public function test_draft_projects_are_hidden_from_the_public(): void
    {
        $draft = Project::create([
            'title' => 'Secret Draft', 'slug' => 'secret-draft', 'short_description' => 's',
            'description' => 'd', 'is_published' => false,
        ]);

        $this->get('/')->assertDontSee('Secret Draft');
        $this->get(route('projects.show', $draft->slug))->assertNotFound();
        $this->actingAs($this->admin())->get(route('projects.show', $draft->slug))->assertOk();
    }

    public function test_guests_cannot_access_admin_routes(): void
    {
        foreach (['dashboard', 'projects.index', 'projects.create', 'skills.index', 'messages.index', 'profile.edit'] as $name) {
            $this->get(route('admin.'.$name))->assertRedirect(route('admin.login'));
        }

        $this->post(route('admin.projects.store'), $this->projectPayload())->assertRedirect(route('admin.login'));
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_non_admin_users_are_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_log_in_and_log_out(): void
    {
        $admin = User::factory()->create(['is_admin' => true, 'password' => 'secret-pass-123']);

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'secret-pass-123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post(route('admin.logout'))->assertRedirect(route('home'));
        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }

    public function test_wrong_password_fails_and_login_is_rate_limited(): void
    {
        $admin = $this->admin();

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'wrong'])
                ->assertSessionHasErrors('email');
        }

        $this->post(route('admin.login.store'), ['email' => $admin->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_create_project_with_image(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.projects.store'), $this->projectPayload([
                'thumbnail' => UploadedFile::fake()->image('shot.jpg', 800, 600),
            ]))
            ->assertRedirect(route('admin.projects.index'));

        $project = Project::firstOrFail();
        $this->assertNotNull($project->thumbnail);
        Storage::disk('public')->assertExists($project->thumbnail);
        $this->assertSame('Web App', $project->category->name);
    }

    public function test_invalid_image_types_and_sizes_are_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.projects.store'), $this->projectPayload([
                'thumbnail' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            ]))
            ->assertSessionHasErrors('thumbnail');

        $this->actingAs($admin)
            ->post(route('admin.projects.store'), $this->projectPayload([
                'thumbnail' => UploadedFile::fake()->image('big.jpg')->size(3000),
            ]))
            ->assertSessionHasErrors('thumbnail');

        $this->assertDatabaseCount('projects', 0);
    }

    public function test_updating_without_new_image_keeps_the_old_one_and_new_image_replaces_it(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.projects.store'), $this->projectPayload([
            'thumbnail' => UploadedFile::fake()->image('one.jpg'),
        ]));
        $project = Project::firstOrFail();
        $old = $project->thumbnail;

        $this->actingAs($admin)->put(route('admin.projects.update', $project), $this->projectPayload(['title' => 'Renamed']))
            ->assertRedirect(route('admin.projects.index'));
        $project->refresh();
        $this->assertSame('Renamed', $project->title);
        $this->assertSame($old, $project->thumbnail);
        Storage::disk('public')->assertExists($old);

        $this->actingAs($admin)->put(route('admin.projects.update', $project), $this->projectPayload([
            'thumbnail' => UploadedFile::fake()->image('two.png'),
        ]));
        $project->refresh();
        $this->assertNotSame($old, $project->thumbnail);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($project->thumbnail);
    }

    public function test_deleting_a_project_removes_its_image(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.projects.store'), $this->projectPayload([
            'thumbnail' => UploadedFile::fake()->image('gone.jpg'),
        ]));
        $project = Project::firstOrFail();
        $path = $project->thumbnail;

        $this->actingAs($admin)->delete(route('admin.projects.destroy', $project))
            ->assertRedirect(route('admin.projects.index'));

        $this->assertDatabaseCount('projects', 0);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_skill_crud(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.skills.store'), ['name' => 'Laravel', 'icon_class' => 'fa-brands fa-laravel', 'sort_order' => 1])
            ->assertRedirect(route('admin.skills.index'));
        $skill = Skill::firstOrFail();

        $this->actingAs($admin)->put(route('admin.skills.update', $skill), ['name' => 'PHP', 'icon_class' => 'fa-brands fa-php', 'sort_order' => 2]);
        $this->assertSame('PHP', $skill->fresh()->name);

        $this->actingAs($admin)->post(route('admin.skills.store'), ['name' => 'X', 'icon_class' => 'not an icon'])
            ->assertSessionHasErrors('icon_class');

        $this->actingAs($admin)->delete(route('admin.skills.destroy', $skill));
        $this->assertDatabaseCount('skills', 0);
    }

    public function test_contact_form_saves_message_and_blocks_bots(): void
    {
        $valid = [
            'name' => 'Visitor', 'email' => 'visitor@example.com', 'subject' => 'Hello',
            'message' => 'This is a test message.', 'website' => '',
            'started_at' => encrypt(now()->subMinute()->timestamp),
        ];

        $this->post(route('contact.store'), $valid)->assertSessionHas('success');
        $this->assertDatabaseCount('messages', 1);

        // Honeypot filled
        $this->post(route('contact.store'), array_merge($valid, ['website' => 'http://spam.test']));
        // Submitted instantly
        $this->post(route('contact.store'), array_merge($valid, ['started_at' => encrypt(now()->timestamp)]));
        $this->assertDatabaseCount('messages', 1);

        // Validation
        $this->post(route('contact.store'), array_merge($valid, ['email' => 'nope']))->assertSessionHasErrors('email');
    }

    public function test_message_management(): void
    {
        $admin = $this->admin();
        $message = Message::create(['name' => 'A', 'email' => 'a@example.com', 'subject' => 'S', 'body' => 'Body text here']);

        $this->actingAs($admin)->get(route('admin.messages.show', $message))->assertOk()->assertSee('Body text here');

        $this->actingAs($admin)->patch(route('admin.messages.read', $message));
        $this->assertTrue($message->fresh()->isRead());

        $this->actingAs($admin)->delete(route('admin.messages.destroy', $message));
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_profile_update_appears_on_homepage(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.profile.update'), [
            'full_name' => 'Jane Example', 'title' => 'Engineer', 'hero_description' => 'Hero text', 'biography' => 'Bio text',
            'motivation' => 'keep going', 'email' => 'jane@example.com',
            'github_url' => '', 'instagram_url' => '',
            'availability_enabled' => '1', 'availability_text' => 'Open',
            'profile_image' => UploadedFile::fake()->image('me.png', 600, 800),
        ])->assertRedirect(route('admin.profile.edit'));

        $this->get('/')->assertSee('Jane')->assertSee('Engineer')->assertSee('Keep going');
    }
}
