<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// auth
Auth::routes();

// projects
Route::resource('projects', 'ProjectController');

// home
Route::get('/home', 'HomeController@index')->name('home');

// tasks
Route::patch('tasks/{task}', 'ProjectTasksController@update');
Route::delete('tasks/{task}', 'ProjectTasksController@destroy');
Route::post('tasks/{task}/assign', 'UsersTasksController@store');
Route::delete('tasks/{task}/assign/{user}/delete', 'UsersTasksController@destroy');
Route::post('projects/{project}/tasks', 'ProjectTasksController@store');

Route::get('/', function () {
    return view('welcome');
});
