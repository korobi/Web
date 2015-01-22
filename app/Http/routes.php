<?php

Route::get('/', 'WelcomeController@index');
Route::get('/auth/github', 'AuthController@index');