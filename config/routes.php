<?php
use Ouzo\Routing\Route;

Route::get('/', 'tests#test');
Route::get('/test', 'tests#test');

Route::get('/client/pesel', 'clients#findByPesel');
Route::get('/client/surname', 'clients#findBySurname');

Route::get('/user/name','users#findByName');
Route::get('/user/surname','users#findBySurname');

Route::put('/user/password', 'users#changePassword');

Route::post('/user/phone', 'users#makePhone');
Route::get('/user/phones', 'users#getPhones');

Route::post('/user/sale', 'users#addSale');
Route::get('/user/sales', 'users#getSales');

Route::get('/report/user/phones/:interval', 'reports#phones');
Route::get('/report/user/sales', 'reports#sales');
Route::get('/report/user/:id', 'reports#user');

Route::get('/mail', 'mails#singleMail');
Route::post('/mails', 'mails#manyMails');
