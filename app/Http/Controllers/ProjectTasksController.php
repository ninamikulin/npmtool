<?php

namespace App\Http\Controllers;

use App\Project;
use App\Task;
use Illuminate\Http\Request;

class ProjectTasksController extends Controller
{
       
    public function update(Task $task)
    {
        
        $this->authorize('edit', $task);

        if (request()->description) {
            $attributes = $this->validateTask();
            $description = $attributes['description'];
        }else{
            $description =  $task->description;
        }

        $task->update([
    		'completed' => request()->has('completed'),
            'description'=> $description
    	]);
    	return back();
    }

    public function store(Project $project, Task $task)
    {
    
        $this->authorize('edit', $task);

        $attributes = $this->validateTask();
        
        $attributes['user_id'] = auth()->id();
    	
        $project->addTask($attributes);

    	return back();
    }
    
    public function validateTask()
    {
        return request()->validate([
            'description' => 'required']);
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('edit', $task);

        $project= $task->project;
        $task->delete();
        

        return redirect("/projects/{$project->id}");
    }


}