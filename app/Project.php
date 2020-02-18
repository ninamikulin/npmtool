<?php

namespace App;

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

	
	//-------------------------------------
    // RELATIONSHIPS
    //-------------------------------------

    // belongs to one user
    public function user()
    {
    	return $this->belongsTo(User::class);
    }
}
