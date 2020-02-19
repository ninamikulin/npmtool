<?php

namespace App\Http\Controllers;

use App\Task;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UsersTasksController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('check.if.admin');
    // }

    public function store(Task $task, Request $request)
    {

        try{
            $task->users()->attach($this->validateTask());

        } catch (QueryException $errors){

           return back()->withErrors('Duplicate entry.');
        }
        
        
    	return back()->with([]);
    }

    public function destroy(Task $task, User $user)
    {
    	$task->users()->detach($user);

    	return back();
    }

  	public function validateTask()
    {
        return request()->validate([
            'assigned_to' => 'required']);
    }
}
