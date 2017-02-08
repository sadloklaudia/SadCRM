<?php
use Ouzo\Routing\Route;

Route::get('/client/pesel/:pesel', 'clients#findByPesel');
Route::get('/client/surname', 'clients#findBySurname');

Route::get('/user/name','users#findByName');
Route::get('/user/surname','users#findBySurname');