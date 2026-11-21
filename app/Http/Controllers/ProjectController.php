<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project)
    {
        // Drafts are visible to signed-in admins only (preview).
        abort_unless($project->is_published || $request->user()?->is_admin, 404);

        $project->load('category');

        return view('public.project-show', compact('project'));
    }
}
