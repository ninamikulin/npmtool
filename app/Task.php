<?php

namespace App;

use App\Project;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded= [];

    //-------------------------------------
    // RELATIONSHIPS
    //-------------------------------------

    // belongs to one project
    public function project()
    {
    	return $this->belongsTo(Project::class);
    }

   	// belongs to many users
    public function users()
    {
    	return $this->belongsToMany(User::class)->withTimestamps();
    }
}