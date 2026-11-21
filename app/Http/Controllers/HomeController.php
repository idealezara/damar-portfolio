<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            // Same table and same "is_published" column the admin form writes to.
            'projects' => Project::published()->with('category')->latest()->get(),
            'categories' => Category::whereHas('projects', fn ($q) => $q->published())->orderBy('name')->get(),
            'skills' => Skill::orderBy('sort_order')->orderBy('name')->get(),
            // Shown to signed-in admins only, so hidden drafts are never a mystery.
            'draftCount' => auth()->user()?->is_admin ? Project::where('is_published', false)->count() : 0,
        ]);
    }
}
