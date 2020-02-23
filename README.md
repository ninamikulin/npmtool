# PMTool

1. [About](#about) 
2. [Basic Laravel Auth](#basic-laravel-auth) 
3. [CRUD for Projects](#crud-for-projects)  
    i. [CREATE project](#create-project)  
    ii. [READ project](#read-project)  
    iii. [UPDATE project](#update-project)   
    iv. [DELETE project](#delete-project) 
4. [CRUD for Tasks](#crud-for-tasks)  
	i. [Create, edit and complete tasks](#create-edit-and-complete-tasks)
    - [CREATE task](#create-task)  
    - [EDIT and UPDATE task](#edit-and-update-task)  
    - [DELETE task](#delete-task)  
    
	ii. [Task assignment](#task-assignment) 
    - [Assign tasks to users](#assign-tasks-to-users)
<<<<<<< Updated upstream
    - [Unassign tasks from users](#unassign-tasks-from-users)
5. [Migrations](#migrations) 
5. [Eloquent relationships](#eloquent-relationships) 
6. [Policies](#policies)
=======
    - [Unassign tasks to users](#unassign-tasks-to-users)
5. [Migrations](#migrations)  
6. [Creating and assigning tasks](#creating-and-assigning-tasks)  
	i. [Models](#models)  
    ii. 
7. [Rich text editor](#rich-text-editor)
8. [Middleware](#middleware)
9. [Gates and Policies](#gates-and-policies)
>>>>>>> Stashed changes
   
## About 
PMTool is a simple project management tool made with Laravel 6. 

   * Basic Laravel login is used for creating accounts and authenticating users.  
   * Users can view, create, edit and delete their projects and tasks.
   * Users can assign tasks to other users, and complete tasks.
   * The projects are sorted by due date, the tasks are sorted by status (`completed`, `to do`) and by the latest created. 
   * The admin of the website can view, edit and delete all projects and tasks.  
   * Laravel's auth middleware is used for checking if the user is authenticated.  
   * A gate has been created and before method is used to define a callback that is run before all other authorization checks.
## Basic Laravel Auth
Create basic Laravel auth: 
- `composer require laravel/ui --dev`
- `npm install && npm run dev`
- `php artisan ui vue --auth` - installs a layout view, registration and login views, routes for all authentication end-points and a HomeController.

## CRUD for Projects

### Create project

To create a new project 2 `ProjectController` methods are used:

- `create` -> returns view with form to create new company

<details> 
<summary>store -> persists the new project in the DB  </summary>  

- validates the request attributes  
- persists the new project to the DB 
- displays a flash message when the project is created 

```php
// /app/Http/Controllers/ProjectController.php

// persists the project to the DB
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
```
</details>

### Read project

<details>
<summary>view all projects </summary>

- returns a view with all the projects ordered by deadline - due date

```php
// /app/Http/Controllers/ProjectController.php

// returns view with projects
public function index()
{    
  $projects = Project::orderBy('deadline','asc')->paginate(10);

  return view('projects.index', ['projects' => $projects]);
}
```
</details>
<details>
<summary>show  one project  </summary>

- shows details of one project with the associated tasks and all the users to enable task assignment   

```php
/app/Http/Controllers/ProjectController.php

// shows one project
public function show(Project $project)
{
  return view('projects.show', ['project' => $project, 'users' => User::all()]);
}

```
</details>

### Update project

To update an existing project 2 `ProjectController` methods are used:

- `edit` -> returns view with form to edit an existing project

<details> 
<summary> update -> persists the changes to the DB</summary> 

- validates the request attributes  
- persists the changes to the DB  
- displays flash message  

```php
// /app/Http/Controllers/ProjectController.php

// persists the changes to the DB
public function update(Project $project)
{
  // server-side validation
  $validatedProject = $this->validateProject();

  // sets additional attributes
  $validateProject['user_id'] = auth()->id();

  // updates project
  $project->update($validatedProject);

  // displays flash message 
  session()->flash('message', 'Your project has been updated.');

  return redirect("/projects/{$project->id}");

}
```
</details>

### Delete project

<details> 
<summary> destroy-> deletes the record from the DB</summary>

```php
// /app/Http/Controllers/ProjectController.php

 // deletes from DB
 public function destroy(Project $project)
 {     
   $this->authorize('edit', $project);
   $project->delete();

  // displays flash message 
  session()->flash('message', 'Project deleted.');

  return redirect('/projects');

}
```
</details>

## CRUD for Tasks

Two controllers were created to handle the logic of task assignment to projects and user assignment to tasks:  

- `ProjectTasksController.php`  
- `UsersTasksController.php`  

### Create, edit and complete tasks

#### Create task

<details> 
<summary>Controller: ProjectTasksController@store </summary> 

- validates the request attributes    
- persists the new company to the DB - calls the addTask method on the Project model  

```php
// /app/Http/Controllers/ProjectTasksController.php

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
```
</details>
<details> 
<summary>Model: Project </summary> 

```php
// /app/Project.php

// adds a task to the project
public function addTask($task)
{ 	
    $this->tasks()->create($task);
}
```
</details>
<details> 
<summary>View: projects.show </summary> 

```html
<!--  /resources/views/projects/show.blade.php-->

<!-- Create task -->
<form method="POST" action="/projects/{{ $project->id }}/tasks">
@csrf
	<div class="form-group">
		<input class="form-control" type="text" name="description" placeholder="Describe the task..." required>
	</div>
	<div class="row d-flex justify-content-center">
		<button type="submit" class="btn btn-success mb-1">Add Task</button>
	</div>
</form>
```
</details>

#### Edit and Update task

<details> 
<summary>Controller: ProjectTasksController@update </summary>

- checks which attributes have been changed and persists the changes to the DB    

```php
// /app/Http/Controllers/ProjectTasksController.php

// persists changes to the task
public function update(Task $task)
{
  // policy to authorize the user to update
  $this->authorize('edit', $task);

  // checks if the description has been changed, sets the updated attributes
  if (request()->description) {
  	$attributes = $this->validateTask();
  	$description = $attributes['description'];

  } else {
  	$description =  $task->description;
  }
  
  // updates the task
  $task->update([
  //checks if completed attribute in request (if task has been completed)
  'completed' => request()->has('completed'),
  //setting the description attribute
  'description' => $description
  ]);

  return back();
}
```
</details>
<details> 
<summary>View: projects.show </summary> 

```html
<!--  /resources/views/projects/show.blade.php-->

@foreach($project->tasks()->orderBy('completed', 'asc')->latest()->get() as $task)
<<<<<<< Updated upstream
<tbody>
<tr style="{{$task->completed ? 'background-color:rgb(56, 193, 114,0.2);' : ''}}">
    <!-- Complete task checkbox -->
    <td>
        <form method="POST" action="/tasks/{{$task->id}}" id="completeTask">
        @method('PATCH')
        @csrf
        @can('edit', $task)
            <input type="checkbox" class="form-check-input" name="completed" onChange="this.form.submit()" {{ $task->completed ? 'checked' : ''}}> 
        @endcan
            <label style="{{ $task->completed ? 'color:#38c172' : 'color:#E3342F'}}" ><strong>{{ $task->completed ? 'Completed!' : 'To do'}}</strong></label>
        </form>
    </td>
    <label></label>

    <!-- Editable task description with collapsable textarea -->
    <td style="width:400px;"> 
        <a  data-toggle="collapse" href="#collapse-{{$task->id}}" role="button" aria-expanded="false" aria-controls="collapseExample" style="width:100px;">{{$task->description}}</a>
        <div class="collapse" id="collapse-{{$task->id}}"> 
            <form method="POST" action="/tasks/{{$task->id}}" style="margin-bottom: 0px!important;">
                @csrf
                @method('PATCH')	     
                <textarea  type="text" class="form-control" name="description" cols="8" rows="4">{{$task->description}}</textarea>
                <button class="btn btn-success mt-1 mb-0" type="button" id="button-addon2" onclick="this.form.submit()"> Save</button>
            </form>
        </div>
    </td>
=======
   	<tbody>
   		<tr style="{{ $task->completed ? 'background-color:rgb(56, 193, 114,0.2);' : '' }}">
		<!-- Complete task checkbox -->	
       	<td>
    			<form method="POST" action="/tasks/{{ $task->id }}" id="completeTask">
    			@method('PATCH')
    			@csrf
    			@can('edit', $task)
    				<input type="checkbox" class="form-check-input" name="completed" onChange="this.form.submit()" {{ $task->completed ? 'checked' : ''}}> 
    			@endcan
    				<label style="{{ $task->completed ? 'color:#38c172' : 'color:#E3342F'}}"><strong>{{ $task->completed ? 'Completed!' : 'To do' }}</strong></label>
    			</form>
    		</td>
		<label></label>

    		<!-- Editable task description with collapsable textarea -->
    	  <td style="width:400px;"> 
    			<a  data-toggle="collapse" href="#collapse-{{ $task->id }}" role="button" aria-expanded="false" aria-controls="collapseExample" style="width:100px;">{{ $task->description }}</a>
    			<div class="collapse" id="collapse-{{ $task->id }}"> 
    				<form method="POST" action="/tasks/{{ $task->id }}" style="margin-bottom: 0px!important;">
    					@csrf
    					@method('PATCH')	     
    	       		<textarea  type="text" class="form-control" name="description" cols="8" rows="4">{{$task->description}}</textarea>
    	          	<button class="btn btn-success mt-1 mb-0" type="button" id="button-addon2" onclick="this.form.submit()">Save</button>
    				</form>
    			</div>
    		</td>
>>>>>>> Stashed changes
```
</details>

#### Delete task

<details> 
<summary>Controller: ProjectTasksController@destroy</summary>

```php
// /app/Http/Controllers/ProjectTasksController.php

// deletes record from DB
public function destroy(Project $project, Task $task)
{
  // policy to authorize the user to delete task
  $this->authorize('edit', $task);

  // deletes record from DB
  $task->delete();

  return redirect("/projects/{$project->id}");
}
```
</details>
<details>
<summary>View: projects.show </summary> 

```html
<!--  /resources/views/projects/show.blade.php-->

<!--Delete task -->
<td>
  <form  method="POST" action="/tasks/{{ $task->id }}" style="margin-top: 0px!important;">
  @method('DELETE')
  @csrf
  <button class="btn btn-danger btn-sm mt-0" onClick="this.form.submit()">Delete task</button>
  </form>
</td>	
```
</details>

### Task assignment

#### Assign tasks to users

<details> 
<summary>Controller: UsersTasksController@store </summary> 

- checks if entry exists in pivot table (a user can only be assigned to a task once   
- creates the entry if the record doesn't exist  

```php
// /app/Http/Controllers/UsersTasksController.php

// assigns the task to a user
public function store(Task $task)
{
    // checks if enrty exists in pivot table (a user can only be assigned to a task once)
    // creates the entry if the record doesn't exist
    try{
<<<<<<< Updated upstream
        $task->users()->attach($this->validateTask());

    } catch (QueryException $errors){

       return back()->withErrors('Duplicate entry.');
    }      
    return back();
=======
      
      $task->users()->attach($this->validateTask());

    } catch (QueryException $errors) {

       return back()->withErrors('Duplicate entry.');
    }  

	return back();
>>>>>>> Stashed changes
}
```
</details>
<details>
<summary>View: projects.show </summary> 

```html
<!--  /resources/views/projects/show.blade.php-->

<!-- assign -->
@foreach($users as $assignedUser)
@if(!$assignedUser->tasks->firstwhere('id',$task->id))
  <form method="POST" action="/tasks/{{ $task->id }}/assign" style="margin-bottom: 0px!important;">
  @csrf
  <button class="btn btn-outline-secondary btn-sm mt-0 mb-0" onClick="this.form.submit()" style="width:100px;" type="link"><input type="hidden" name="assigned_to" value="{{ $assignedUser->id }}">{{ $assignedUser->name }}</button></li>
  </form>
@endif
@endforeach		
```
</details>

#### Unassign tasks to users

<details> 
<summary>Controller: UsersTasksController@destroy </summary> 

```php
// /app/Http/Controllers/UsersTasksController.php

// unassigns the task 
public function destroy(Task $task, User $user)
{ 
  $task->users()->detach($user);

	return back();
}
```
</details>
<details>
<summary>View: projects.show </summary> 

```html
<!--  /resources/views/projects/show.blade.php-->

@foreach ($task->users as $user)
<!-- unassign -->
  <form method="POST" action="/tasks/{{ $task->id }}/assign/{{ $user->id }}/delete" style="margin-bottom: 0px!important;">
  @method('DELETE')
  @csrf
  <button class="btn btn-outline-success btn-sm mt-0 mb-0" style="width:100px;" onClick="this.form.submit()">&#9989; {{ $user->name }}</button>
  </form>
@endforeach
```
</details>

## Migrations

<details><summary>Projects table</summary>

```php
class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description');
            $table->datetime('deadline');
            $table->timestamps();

            // foreign key constraint - add user_id key to projects table
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
```
</details>
<details><summary>Tasks table</summary>

```php
class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('project_id');
            $table->string('description');
            $table->boolean('completed')->default(false);
            $table->timestamps();

            //foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
```
</details>
<details><summary>task_user pivot table</summary>

```php
class CreateTaskUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('task_id');

            $table->timestamps();

            // a unique entry is a unique combination of the two identifiers - foreign keys
            $table->unique(['user_id', 'task_id']);

            // foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_user');
    }
}
```
</details>

## Eloquent relationships

<details><summary>User</summary>

- `hasMany` Projects - one to many
```php
// has many Projects 
public function projects()
{
return $this->hasMany(Project::class);
}

```

- `belongsToMany` Tasks - many to many 
```php
//belongs to many Tasks
public function tasks()
{
return $this->belongsToMany(Task::class)->withTimestamps();
}

```

- check if admin  
```php
// checks if admin - user with id==1 is admin 
public function isAdmin()
{
  if ($this->id == 1)
  {
  	return true;
  }
}

``` 
</details>
<details><summary>Project</summary>

- `belongsTo` one User  - one to many 
```php
// belongs to one user
public function user()
{
	return $this->belongsTo(User::class);
}
```

- `hasMany` Tasks - one to many 
```php
// has many tasks
public function tasks()
{
return $this->hasMany(Task::class);
} 
```

</details>
<details><summary>Task</summary>

- `belongsTo` one project - one to many

```php
// belongs to one project
public function project()
{
	return $this->belongsTo(Project::class);
}
```

- `belongsToMany` users - many to many

```php

// belongs to many users
public function users()
{
	return $this->belongsToMany(User::class)->withTimestamps();
}
```
</details>


## Policies

<details><summary>Project policy -> only project owners can edit and delete a project</summary>

```php
class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function edit(User $user, Project $project)
    {
        return $project->user_id === $user->id;
    }
}
```
</details>
<details><summary>Task policy -> project task owners can edit and delete a task</summary>

```php
class TaskPolicy
{
    use HandlesAuthorization;

    public function edit(User $user, Task $task)
    {
        return $task->user_id === $user->id;
    }
}
```
</details>
<details><summary>Registering policies and Gate functionality </summary>

```php
class AuthServiceProvider extends ServiceProvider
{
  /**
  * The policy mappings for the application.
  *
  * @var array
  */
  protected $policies = [
  'App\Project' => 'App\Policies\ProjectPolicy',
  'App\Task' => 'App\Policies\TaskPolicy',
  ];

  /**
  * Register any authentication / authorization services.
  *
  * @return void
  */
  public function boot()
  {
  	// register policies
    $this->registerPolicies();
	
    // run the logic in the gate before all other auth checks
    Gate::before(function ($user, $ability) {
    return $user->isAdmin();
    });
  }
}

```
</details>

