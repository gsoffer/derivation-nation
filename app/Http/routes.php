<?php

Route::get('/', 'HomeController@index');
Route::get('/contact_us', 'HomeController@contactUs');
Route::get('/mathproofs', 'MathproofsController@index');
Route::get('/mathproofs/create', ['middleware' => 'auth', 'uses' => 'MathproofsController@create']);
Route::get('/mathproofs/recent', 'MathproofsController@index');
Route::get('/mathproofs/recent/{page}', 'MathproofsController@index');
Route::get('/mathproofs/search/{search}/{page}', 'MathproofsController@search');
Route::get('/mathproofs/search/{search}', 'MathproofsController@search');
Route::get('/mathproofs/{slug_id}', 'MathproofsController@show');
Route::get('/auth/register', ['middleware' => 'guest', 'uses' => 'Auth\AuthController@getRegister']);
Route::get('/auth/login', ['middleware' => 'guest', 'uses' => 'Auth\AuthController@getLogin']);
Route::get('/auth/logout', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@getLogout']);
Route::get('/password/email','Auth\PasswordController@getEmail');
Route::get('/password/reset/{token}', 'Auth\PasswordController@getReset');
Route::get('/auth/verify_email/{confirmation_code}', ['middleware' => 'guest', 'uses' => 'Auth\AuthController@verifyEmail']);
Route::get('/auth/me', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@me']);
Route::get('/auth/me/update_user', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@update_me']);

Route::post('/contact_us', 'HomeController@emailUs');
Route::post('/mathproofs', ['middleware' => 'auth', 'uses' => 'MathproofsController@store']);
Route::post('/mathproofs/search', 'MathproofsController@form_search');
Route::post('/comments', ['middleware' => 'auth', 'uses' => 'CommentsController@store']);
Route::post('/accurateflags', ['middleware' => 'auth', 'uses' => 'AccurateflagsController@store']);
Route::post('/auth/register', ['middleware' => 'guest', 'uses' => 'Auth\AuthController@postRegister']);
Route::post('/auth/login', ['middleware' => 'guest', 'uses' => 'Auth\AuthController@postLogin']);
Route::post('/password/email', 'Auth\PasswordController@postEmail');
Route::post('/password/reset', 'Auth\PasswordController@postReset');
Route::post('/auth/update', ['middleware' => 'auth', 'uses' => 'Auth\AuthController@updateUser']);


//TO SEE QUERIES GENERATED
//Uncomment this and make sure the controller method doesn't include a "return",
//so that just the query gets printed to the screen
/*
Event::listen('illuminate.query', function($query)
{
    var_dump($query);
});
*/
