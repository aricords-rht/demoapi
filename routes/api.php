<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Redirect any unathenticated requests to the API documentation.
Route::any('/', 'DocumentationController@index')->name('login');

Route::group(['middleware' => ['auth:api']], function()
{
    Route::get   ('tasks',        'TaskController@index');
    Route::post  ('tasks',        'TaskController@create');
    Route::get   ('tasks/{task}', 'TaskController@read');
    Route::put   ('tasks/{task}', 'TaskController@update');
    Route::delete('tasks/{task}', 'TaskController@delete');
});

