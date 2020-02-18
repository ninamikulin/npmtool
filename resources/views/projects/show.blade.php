@extends('layouts.app')

@section('content')

<div class="text-center">
	<h1 class="mt-5 mb-4 p-3"><strong>{{$project->name}}</strong>
	</h1>
</div>
<div class="row d-flex justify-content-center text-center p-2">
	<p>{{$project->description}}</p>
</div>
<div class="row d-flex justify-content-center">
	<p class="text-danger">Due date:</p>
</div>	
<div class="row d-flex justify-content-center">	  
	<form method="POST" action="/projects/{{$project->id}}">
		@csrf
		@method('PATCH')
		<div class="form-group" style="width:300px;">
       		<input type="hidden" name="name" value="{{$project->name}}">
       		<input type="hidden" name="description" value="{{$project->description}}">
           	<input type="date" name="deadline" onchange="this.form.submit()" class="form-control" value="{{$project->deadline->format('Y-m-d')}}" required>
        </div>
	</form>
</div>	
<div class="row d-flex justify-content-center p-1">
	<p>Created: {{$project->created_at->format('d-m-Y')}}</p>
</div>
<div class="row d-flex justify-content-center  p-1">
	<p>Owner: {{$project->user->name}}</p>
</div>
<div class="row d-flex justify-content-center  p-1">
<a href="/projects/{{$project->id}}/edit"><button class="btn btn-primary" type="link">Edit Project</button></a>
</div>
@endsection