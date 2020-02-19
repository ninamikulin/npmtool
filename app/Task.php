<?php

namespace App;

use App\Project;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded= [];

    public function project()
    {
    	return $this->belongsTo(Project::class);
    }

    public function users()
    {
    	return $this->belongsToMany(User::class)->withTimestamps();
    }
}