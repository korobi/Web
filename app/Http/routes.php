<?php

Route::get('/', 'WelcomeController@index');
Route::get('/login', 'AuthController@redirectToGitHub');
Route::get('/auth/github', 'AuthController@getUserDetails');