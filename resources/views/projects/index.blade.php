
@extends('layouts.app')

@section('content')
<div class="text-center">
	<h1 class="mt-5 mb-3 p-3"><strong>All projects</strong>
	</h1>
</div>
<div class="row d-flex justify-content-center  p-3">
<a href="/projects/create"><button class="btn btn-primary" type="link">Create Project</button></a>
</div>
@if (!empty($projects))
<table class="table table-striped text-center p-5 ">
	<thead>
	   	<tr class="p-4">
	   		<th scope="col"><h4>Project name</h4></th>
		   	<th scope="col"><h4>Due date</h4></th>
		   	<th scope="col"><h4>Owner</h4></th>

		   	<th scope="col"></th>
		   	<th scope="col"></th>
		</tr>
		</thead>
		@foreach($projects as $project)
   	<tbody>
   		<tr>
   			<td><strong>{{$project->name}}</strong></th>
		    <td>{{$project->deadline->format('d-m-Y')}}</td>
		    <td>{{$project->user->name}}</td>
		    <td><a href="{{route('projects.show', ['project'=> $project])}}">Go to project</a></td>
		    <td><a href="#deleteModal" rel="modal:open"><button  type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button></a></td>
	    </tr>
	  </tbody>
	  @endforeach
</table>
@else
<li class="list-group-item text-center">You have no projects.</li>
@endif
<!-- Modal HTML embedded directly into document -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Project {{ $project->name}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Are you sure you want to permanently delete this project?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">No, go back.</button>
        <form method="POST" action="/projects/{{$project->id}}">
	        @method('DELETE')
	        @csrf
	        <button type="submit" class="btn btn-danger">Delete</button>
    	</form>
      </div>
    </div>
  </div>
</div>
@endsection