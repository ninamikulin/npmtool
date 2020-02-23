@extends('layouts.app')
@section('content')
<div class="text-center">
	<h1 class="mt-5 mb-5 "><strong>All projects</strong>
	</h1>
</div>
<div class="row d-flex justify-content-center mb-4">
<a href="/projects/create"><button class="btn btn-success" type="link">Create Project</button></a>
</div>
@if (!empty($projects))
<table class="table table-striped text-center p-5 ">
	<thead>
	   	<tr class="p-4">
	   		<th scope="col"><h4>Project name</h4></th>
	   		<th scope="col"><h4>Tasks</h4></th>
		   	<th scope="col"><h4>Due date</h4></th>
		   	<th scope="col"><h4>Owner</h4></th>
		   	<th scope="col"></th>
		   	<th scope="col"></th>
		</tr>
		</thead>
		@foreach($projects as $project)
   	<tbody>
   		<tr>
   			<td><strong>{{$project->name}}</strong></td>
   			<td style="text-align:left; width: 400px;">
   				<ul>
   				@foreach($project->tasks()->orderBy('completed', 'asc')->latest()->get() as $task)
   				<li><span class="badge badge-{{$task->completed ? 'success': 'danger'}}" >{{ $task->completed ? 'completed': 'to do' }}</span> {{ $task->description }} </li> 
   				@endforeach
   				</ul>
   			</td>
		    <td>{{ $project->deadline->format('d-m-Y') }}</td>
		    <td>{{ $project->user->name }}</td>
		    <td><a href="{{route('projects.show', ['project'=> $project])}}">More...</a></td>
		    <td></td>
	    </tr>
	  </tbody>
	  @endforeach
</table>
@else
<li class="list-group-item text-center">You have no projects.</li>
@endif
@endsection