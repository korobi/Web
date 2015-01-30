<?php

Route::get('/', 'WelcomeController@index');
Route::get('/login', 'GitHubAuthController@redirectToGitHub');
Route::get('/auth/github', 'GitHubAuthController@getUserDetails');