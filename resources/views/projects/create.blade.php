@extends('layouts.app')

@section('content')

<div class="text-center">
    <div>
        <h1 class="mt-5 mb-3 p-3"><strong>Create Project</strong>
        </h1>
    </div>

<div >
    <form  method="POST" action="/projects">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" placeholder="Project Name" value="{{old('name')}}" required>  
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" name="description" class="form-control" placeholder="Description" value="{{old('description')}}" required>
        </div>
        <div class="form-group">
            <label for="website">Deadline</label>
            <input type="date" name="deadline" class="form-control" value="{{old('website')}}" required>
        </div>
         
        <button type="submit" class="btn btn-primary mt-3">Create</button>
    </form>
</div>

@endsection