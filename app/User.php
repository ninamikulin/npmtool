<?php

namespace App;

use App\Project;
use App\Task;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // checks if admin
    public function isAdmin()
    {
        if ($this->id == 1)
        {
            return true;
        }
    }

    //-------------------------------------
    // RELATIONSHIPS
    //-------------------------------------

    // has many Projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    //belongs to many Tasks
    public function tasks()
    {
        return $this->belongsToMany(Task::class)->withTimestamps();
    }
}
