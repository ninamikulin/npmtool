<?php

namespace App\Http\Controllers;

use App\Project;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    // returns view with projects
    public function index()
    {        
        // if user is admin, return all projects, order by deadline
        if (auth()->user()->isAdmin()) {
            $projects = Project::orderBy('deadline','asc');
        }
        // if user is not admin, return all projects owned by the user, order by deadline
        else{
            $projects = Project::where('user_id', auth()->id())->orderBy('deadline','asc');
        }
        //paginate the results
        $projects = $projects->paginate(10);

        return view('projects.index', ['projects'=>$projects]);
    }

    // returns view with a form to create a project
    public function create()
    {
        return view('projects.create');
    }

    // persists the company to the DB
    public function store()
    {
        // server-side validation
        $validatedProject = $this->validateProject();

        // sets additional attributes
        $validatedProject['user_id'] = auth()->id();

        // creates project
        $project = Project::create($validatedProject);

        // displays flash message 
        session()->flash('message', 'Your project has been created.');
        
        return redirect('/projects');
    }

    // shows one project
    public function show(Project $project)
    {
        return view('projects.show', ['project'=> $project]);
    }

    // returns view with a form to edit an existing project
    public function edit(Project $project)
    {
        return view('projects.edit', ['project'=> $project]);
    }

    // persists the changes to the DB
    public function update(Project $project)
    {
        // server-side validation
        $validatedProject= $this->validateProject();

        // sets additional attributes
        $validateProject['user_id'] = auth()->id();

        // updates project
        $project->update($validatedProject);

        // displays flash message 
        session()->flash('message', 'Your project has been updated.');

        return redirect("/projects/{$project->id}");

    }

    // deletes from DB
    public function destroy(Project $project)
    {     
        $project->delete();

        // displays flash message 
        session()->flash('message', 'Project deleted.');

        return redirect("/projects");

    }

    // server-side validation
    public function validateProject()
    {
        return request()->validate([
            'name' => 'required|min:3',
            'description' => 'required|min:5',
            'deadline' => 'required|date:d-m-Y|after:today'
        ]);
    }

}