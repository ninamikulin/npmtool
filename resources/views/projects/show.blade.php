@extends('layouts.app')
@section('content')
<div class="text-center">
	<h1 class="mt-5 mb-5"><strong>{{ $project->name }}</strong>
	</h1>
	<!-- Collapse details -->
	<div class="row d-flex justify-content-center text-center">	
		<a  data-toggle="collapse" href="#collapseDetails" role="button" aria-expanded="false" aria-controls="collapseExample" style="width:100px;">	
	    See details
	  	</a>
		<div class="collapse" id="collapseDetails">
		  <div class="card card-body" style="background-color: #f8fafc; border:none;">
		  	<div class="row d-flex justify-content-center text-center">
		    {{ $project->description }}
			</div>
			<div class="row d-flex justify-content-center mt-3" style="vertical-align: bottom;">
				<strong class="text-danger">Due date: &nbsp</strong>
				<form method="POST" action="/projects/{{$project->id}}" style="margin-top: 0px!important;">
					@csrf
					@method('PATCH')
					<div class="form-group" style="width:300px;">
			       		<input type="hidden" name="name" value="{{ $project->name }}">
			       		<input type="hidden" name="description" value="{{ $project->description }}">
			           	<input type="date" name="deadline" onchange="this.form.submit()" class="form-control" value="{{ $project->deadline->format('Y-m-d') }}" required>
			        </div>
				</form>
			</div>	
			<div class="row d-flex justify-content-center">
				<p><strong>Created:</strong> {{ $project->created_at->format('d-m-Y') }}</p>
			</div>
			<div class="row d-flex justify-content-center">
				<p><strong>Owner: </strong> {{ $project->user->name }}</p>
			</div>
		  </div>
		</div>
	</div>
</div>

<!-- Edit Project and Delete Project Buttons -->
<div class="row d-flex justify-content-center text-center">	
	<a href="/projects/{{ $project->id }}/edit" class="btn btn-success mb-2 mt-3 mr-1" style="width:100px;">Edit Project </a>
	<a href="#deleteModal" rel="modal:open"><button  type="button" class="btn btn-danger mb-2 mt-3" data-toggle="modal" data-target="#deleteModal" style="width:100px;">Delete</button></a>
</div>

<!-- Tasks Table-->
@if (isset($project->tasks))
<table class="table table-striped text-center">
	<thead>
	   	<tr class="p-4">
	 		<th scope="col"><h4>Status</h4></th>
	   		<th scope="col"><h4>Task</h4></th> 
		   	<th scope="col"><h4>Created</h4></th>
		   	<th scope="col"><h4>Assignee</h4></th>
		 	<th scope="col"></th>
		</tr>
		</thead>
	
	@foreach($project->tasks()->orderBy('completed', 'asc')->latest()->get() as $task)
   	<tbody>
   		<tr style="{{$task->completed ? 'background-color:rgb(56, 193, 114,0.2);' : ''}}">
			<!-- Complete task checkbox -->			
   			<td>
				<form method="POST" action="/tasks/{{ $task->id }}" id="completeTask">
				@method('PATCH')
				@csrf
				@can('edit', $task)
					<input type="checkbox" class="form-check-input" name="completed" onChange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}> 
				@endcan
					<label style="{{ $task->completed ? 'color:#38c172' : 'color:#E3342F'}}" ><strong>{{ $task->completed ? 'Completed!' : 'To do' }}</strong></label>
				</form>
			</td>			
			<label></label>
			
			<!-- Description with collapsable textarea -->
		    <td style="width:400px;"> 
				<a  data-toggle="collapse" href="#collapse-{{ $task->id }}" role="button" aria-expanded="false" aria-controls="collapseExample" style="width:100px;">{{ $task->description }}</a>
				<div class="collapse" id="collapse-{{ $task->id }}"> 
					<form method="POST" action="/tasks/{{ $task->id }}" style="margin-bottom: 0px!important;">
						@csrf
						@method('PATCH')	     
			       		<textarea  type="text" class="form-control" name="description" cols="8" rows="4">{{$task->description}}</textarea>
			          	<button class="btn btn-success mt-1 mb-0" type="button" id="button-addon2" onclick="this.form.submit()"> Save</button>
					</form>
				</div>
			</td>
			<!-- Created at -->		
		    <td>{{ $task->created_at->format('d-m-Y') }}</td>	
		 	    	
		 	<!-- Task-> users assignment -->
		 	<td>
		 		@foreach ($task->users as $user)
		 		<!-- unassign -->
				<form method="POST" action="/tasks/{{ $task->id }}/assign/{{ $user->id }}/delete" style="margin-bottom: 0px!important;">
					@method('DELETE')
					@csrf
					<button class="btn btn-outline-success btn-sm mt-0 mb-0" style="width:100px;" onClick="this.form.submit()">&#9989; {{ $user->name }}</button>
				</form>
				@endforeach
				<!-- assign -->
		 		@foreach($users as $assignedUser)
				@if(!$assignedUser->tasks->firstwhere('id',$task->id))
				<form method="POST" action="/tasks/{{ $task->id }}/assign" style="margin-bottom: 0px!important;">
					@csrf
					<button class="btn btn-outline-secondary btn-sm mt-0 mb-0" onClick="this.form.submit()" style="width:100px;" type="link"><input type="hidden" name="assigned_to" value="{{ $assignedUser->id }}">{{ $assignedUser->name }}</button></li>
				</form>
				@endif
				@endforeach	
			</td>
			<!--Delete task -->
			<td>
				<form  method="POST" action="/tasks/{{ $task->id }}" style="margin-top: 0px!important;">
					@method('DELETE')
					@csrf
					<button class="btn btn-danger btn-sm mt-0" onClick="this.form.submit()"> Delete task</button>
				</form>
			</td>	
	    </tr>
	  </tbody>
	  @endforeach
</table>

<!-- Create task -->
<form method="POST" action="/projects/{{$project->id}}/tasks">
	@csrf
	<div class="form-group">
		<input class="form-control" type="text" name="description" placeholder="Describe the task..." required>
	</div>
	<div class="row d-flex justify-content-center">
		<button type="submit" class="btn btn-success mb-1">Add Task</button>
	</div>
</form>

@else
<li class="list-group-item text-center">No tasks yet.</li>
@endif

<!-- delete Project Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Project {{ $project->name }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to permanently delete this project?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success" data-dismiss="modal">No, go back.</button>
        <form method="POST" action="/projects/{{ $project->id }}">
	        @method('DELETE')
	        @csrf
	        <button type="submit" class="btn btn-danger">Delete</button>
    	</form>
      </div>
    </div>
  </div>
</div>
@endsection