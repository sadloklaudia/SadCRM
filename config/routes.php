<?php
use Ouzo\Routing\Route;

Route::get('/hello_world', 'hello_world#index');

Route::get('/', 'users#index');
Route::get('/fresh', 'users#fresh');
Route::get('/:id/edit', 'users#edit');
Route::get('/:id', 'users#show');

Route::post('/', 'users#create');
Route::put('/:id', 'users#update');
Route::delete('/:id', 'users#destroy');
