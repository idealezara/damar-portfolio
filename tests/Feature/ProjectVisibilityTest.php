<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Admin <-> public consistency for projects (reported bug: saved projects not showing). */
class ProjectVisibilityTest extends TestCase
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

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Gallery Project',
            'category' => 'Web App',
            'short_description' => 'Short text',
            'description' => 'Long text',
            'technologies' => 'Laravel',
            'project_url' => '',
            'github_url' => '',
            'is_published' => '1',
        ], $overrides);
    }

    public function test_create_form_defaults_to_published(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.projects.create'))
            ->assertOk()
            ->assertSee('<option value="1" selected>Published</option>', false);
    }

    public function test_published_project_appears_on_public_site_with_success_message(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.projects.store'), $this->payload())
            ->assertRedirect(route('admin.projects.index'))
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'live on the public portfolio'))
            ->assertSessionHas('view_url');

        $this->assertDatabaseHas('projects', ['title' => 'Gallery Project', 'is_published' => 1]);

        $this->get('/')->assertOk()->assertSee('Gallery Project');
    }

    public function test_draft_project_is_hidden_publicly_and_flagged_for_admins(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.projects.store'), $this->payload(['title' => 'Hidden Draft', 'is_published' => '0']))
            ->assertSessionHas('success', fn ($m) => str_contains($m, 'hidden from public visitors'));

        auth()->logout();
        $this->get('/')->assertDontSee('Hidden Draft')->assertDontSee('Admin notice');

        $this->actingAs($admin)->get('/')->assertDontSee('Hidden Draft')->assertSee('Admin notice');
    }

    public function test_editing_and_status_toggle_update_the_public_site(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.projects.store'), $this->payload(['is_published' => '0']));
        $project = Project::firstOrFail();

        $this->actingAs($admin)->patch(route('admin.projects.status', $project));
        $this->assertTrue($project->fresh()->is_published);
        $this->get('/')->assertSee('Gallery Project');

        $this->actingAs($admin)->put(route('admin.projects.update', $project), $this->payload(['title' => 'Renamed Project']));
        $this->get('/')->assertSee('Renamed Project')->assertDontSee('Gallery Project');

        $this->actingAs($admin)->patch(route('admin.projects.status', $project));
        $this->get('/')->assertDontSee('Renamed Project');
    }

    public function test_deleting_a_project_removes_it_from_the_public_site(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.projects.store'), $this->payload());
        $project = Project::firstOrFail();
        $this->get('/')->assertSee('Gallery Project');

        $this->actingAs($admin)->delete(route('admin.projects.destroy', $project));

        $this->get('/')->assertDontSee('Gallery Project');
        $this->get(route('projects.show', $project->slug))->assertNotFound();
    }

    public function test_uploaded_image_url_and_category_filter_render_on_the_public_site(): void
    {
        $this->actingAs($this->admin())->post(route('admin.projects.store'), $this->payload([
            'thumbnail' => UploadedFile::fake()->image('shot.jpg', 800, 600),
        ]));
        $project = Project::firstOrFail();

        $this->get('/')
            ->assertSee('/storage/'.$project->thumbnail, false)
            ->assertSee('data-filter="web-app"', false)
            ->assertSee('data-category="web-app"', false);
    }

    public function test_guests_cannot_toggle_project_status(): void
    {
        $project = Project::create([
            'title' => 'P', 'slug' => 'p', 'short_description' => 's', 'description' => 'd', 'is_published' => false,
        ]);

        $this->patch(route('admin.projects.status', $project))->assertRedirect(route('admin.login'));
        $this->assertFalse($project->fresh()->is_published);
    }
}
