@extends('layouts.app')

@section('content')

<div class="text-center">
    <div>
        <h1 class="mt-5 mb-3 p-3"><strong>Update Project</strong>
        </h1>
    </div>

<div >
    <form  method="POST" action="/projects/{{$project->id}}">
        @csrf
        @method('PATCH')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{$project->name}}" required>  
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" value="{{$project->description}}" required>
        </div>
        <div class="form-group">
            <label for="website">Due date</label>
            <input type="date" name="deadline" class="form-control" value="{{$project->deadline->format('Y-m-d')}}" required>
        </div>
         
        <button type="submit" class="btn btn-primary mt-3">Update</button>
    </form>
</div>

@endsection