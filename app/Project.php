<?php

namespace App;

use App\Task;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
	// mass assignable attributes
	protected $fillable = ['name', 'description', 'user_id', 'deadline'];

	// casts attribute to assigned data types
	protected $casts = [
		'created_at'  => 'datetime',
		'deadline' => 'datetime'];

	// adds a task to the project
	public function addTask($task)
    { 	
    	$this->tasks()->create($task);
    }

	//-------------------------------------
    // RELATIONSHIPS
    //-------------------------------------

    // belongs to one user
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    // has many tasks
    public function tasks()
    {
    	return $this->hasMany(Task::class);
    } 

  
}
