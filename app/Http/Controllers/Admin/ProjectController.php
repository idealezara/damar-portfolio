<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with('category')
            ->when($request->string('q')->trim()->toString(), function ($query, $term) {
                $query->where(fn ($q) => $q
                    ->where('title', 'like', "%{$term}%")
                    ->orWhere('short_description', 'like', "%{$term}%"));
            })
            ->when(in_array($request->query('status'), ['published', 'draft'], true), function ($query) use ($request) {
                $query->where('is_published', $request->query('status') === 'published');
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create', ['categories' => Category::orderBy('name')->get()]);
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->safe()->except(['thumbnail', 'category']);
        $data['category_id'] = $this->resolveCategory($request->input('category'));
        $data['slug'] = Project::uniqueSlug($data['title']);
        $data['thumbnail'] = Media::replace($request->file('thumbnail'), 'projects');

        $project = Project::create($data);

        return $this->saved($project, 'created');
    }

    public function show(Project $project)
    {
        $project->load('category');

        return view('admin.projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', [
            'project' => $project,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $data = $request->safe()->except(['thumbnail', 'category']);
        $data['category_id'] = $this->resolveCategory($request->input('category'));
        // No new file selected => the previous image is kept.
        $data['thumbnail'] = Media::replace($request->file('thumbnail'), 'projects', $project->thumbnail);

        $project->update($data);

        return $this->saved($project->refresh(), 'updated');
    }

    /** Quick Publish / Unpublish from the project list. */
    public function toggleStatus(Project $project)
    {
        $project->update(['is_published' => ! $project->is_published]);

        return $this->saved($project, $project->is_published ? 'published' : 'unpublished');
    }

    public function destroy(Project $project)
    {
        Media::delete($project->thumbnail);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted. It has been removed from the public portfolio.');
    }

    private function saved(Project $project, string $verb)
    {
        $message = $project->is_published
            ? "Project “{$project->title}” was {$verb} and is live on the public portfolio."
            : "Project “{$project->title}” was {$verb} as a draft. It is hidden from public visitors until you publish it.";

        return redirect()->route('admin.projects.index')
            ->with('success', $message)
            ->with('view_url', $project->is_published ? route('projects.show', $project->slug) : null);
    }

    private function resolveCategory(?string $name): ?int
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        return Category::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id;
    }
}
