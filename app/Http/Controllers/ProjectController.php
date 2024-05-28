<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('pages.projects.index');
    }

    public function create(): View
    {
        return view('pages.projects.create');
    }

    public function show(Project $project): View
    {
        return view('pages.projects.show', compact('project'));
    }
}
