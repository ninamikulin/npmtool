<?php

namespace App\Http\Controllers;

use App\Project;
use App\Task;
use Illuminate\Http\Request;

class ProjectTasksController extends Controller
{

    // middleware to only authorize auth users
    public function __construct()
    {
        $this->middleware('auth');
    }

    //-------------------------------------
    // CRUD
    //-------------------------------------

    // persists changes to the task
    public function update(Task $task)
    {
        // policy to authorize the user to update
        $this->authorize('edit', $task);

        // checks if the description has been changed, sets the updated attributes
        if (request()->description) {
            $attributes = $this->validateTask();
            $description = $attributes['description'];
        }else{
            $description =  $task->description;
        }

        // updates the task
        $task->update([
            //checks if completed attribute in request (if task has been completed)
    		'completed' => request()->has('completed'),
            //setting the description attribute
            'description'=> $description
    	]);
    	return back();
    }

    // stores the task to the DB
    public function store(Project $project, Task $task)
    {
        // policy to authorize the user to create task
        $this->authorize('edit', $task);

        // validating attributes
        $attributes = $this->validateTask();
        
        // setting additional attributes
        $attributes['user_id'] = auth()->id();
    	
        // calling the addTask method on the Project model
        $project->addTask($attributes);

    	return back();
    }

    // deletes record from DB
    public function destroy(Project $project, Task $task)
    {
        // policy to authorize the user to delete task
        $this->authorize('edit', $task);

        // deletes record from DB
        $task->delete();

        return redirect("/projects/{$project->id}");
    }

    //-------------------------------------
    // HELPERS
    //-------------------------------------

    // server-side validation
    public function validateTask()
    {
        return request()->validate([
            'description' => 'required']);
    }

}