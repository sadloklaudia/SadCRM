<?php
use Ouzo\Routing\Route;

Route::get('/', 'tests#test');

Route::get('/user','users#find');
Route::get('/user/byId','users#findById');
Route::post('/user/login', 'users#login');
Route::post('/user/create', 'users#createUser');
Route::post('/user/update', 'users#updateUser');
Route::put('/user/password', 'users#changePassword');

Route::get('/client', 'clients#find');
Route::get('/client/byId','clients#findById');
Route::post('/client/create', 'clients#createClient');
Route::post('/client/update', 'clients#updateClient');

Route::post('/address/create', 'addresses#createAddress');
Route::post('/address/update', 'addresses#updateAddress');

Route::get('/user/phones', 'users#getPhones');
Route::post('/user/phone', 'users#makePhone');

Route::post('/user/sale', 'users#addSale');
Route::get('/user/sales', 'users#getSales');

Route::get('/report/user/phones/:interval', 'reports#phones');
Route::get('/report/user/sales', 'reports#sales');
Route::get('/report/user/:id', 'reports#user');

Route::post('/mail', 'mails#singleMail');
Route::post('/mails', 'mails#manyMails');

Route::get('/download', 'downloads#download');