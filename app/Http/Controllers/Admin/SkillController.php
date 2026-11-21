<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SkillRequest;
use App\Models\Skill;

class SkillController extends Controller
{
    public function index()
    {
        return view('admin.skills.index', [
            'skills' => Skill::orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.skills.create');
    }

    public function store(SkillRequest $request)
    {
        Skill::create($this->payload($request));

        return redirect()->route('admin.skills.index')->with('success', 'Skill added.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    public function update(SkillRequest $request, Skill $skill)
    {
        $skill->update($this->payload($request));

        return redirect()->route('admin.skills.index')->with('success', 'Skill updated.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()->route('admin.skills.index')->with('success', 'Skill deleted.');
    }

    private function payload(SkillRequest $request): array
    {
        $data = $request->validated();
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
