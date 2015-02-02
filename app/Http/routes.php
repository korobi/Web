<?php

Route::get('/', 'WelcomeController@index');
Route::get('/auth/login', 'GitHubAuthController@redirectToGitHub');
Route::get('/auth/logout', 'GitHubAuthController@logout');
Route::get('/auth/test', 'GitHubAuthController@testAuth');
Route::get('/auth/github', 'GitHubAuthController@getUserDetails');
