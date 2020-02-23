<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UsersTasksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //-------------------------------------
    // CRUD
    //-------------------------------------

    // assigns the task to a user
    public function store(Task $task)
    {
        // checks if enrty exists in pivot table (a user can only be assigned to a task once)
        // creates the entry if the record doesn't exist
        try{

            $task->users()->attach($this->validateTask());

        } catch (QueryException $errors) {

           return back()->withErrors('Duplicate entry.');
        }      
        
    	return back();
    }

    // unassigns the task 
    public function destroy(Task $task, User $user)
    {
    	$task->users()->detach($user);

    	return back();
    }

    //-------------------------------------
    // HELPERS
    //-------------------------------------

  	public function validateTask()
    {
        return request()->validate([
            'assigned_to' => 'required']);
    }
}
