<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Project;
use App\Models\Skill;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'projects' => Project::count(),
            'published' => Project::published()->count(),
            'messages' => Message::count(),
            'unread' => Message::unread()->count(),
            'skills' => Skill::count(),
        ];

        $activities = collect()
            ->merge(Project::latest('updated_at')->take(4)->get()->map(fn ($p) => [
                'icon' => 'folder',
                'text' => ($p->wasRecentlyCreated || $p->created_at->equalTo($p->updated_at) ? 'Project added: ' : 'Project updated: ').$p->title,
                'at' => $p->updated_at,
            ]))
            ->merge(Message::latest()->take(4)->get()->map(fn ($m) => [
                'icon' => 'message-square',
                'text' => 'Message from '.$m->name,
                'at' => $m->created_at,
            ]))
            ->merge(Skill::latest()->take(3)->get()->map(fn ($s) => [
                'icon' => 'layers',
                'text' => 'Skill added: '.$s->name,
                'at' => $s->created_at,
            ]))
            ->sortByDesc('at')
            ->take(6)
            ->values();

        return view('admin.dashboard', compact('stats', 'activities'));
    }
}
