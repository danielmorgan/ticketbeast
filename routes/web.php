<?php

Route::get('/', function() {
    return redirect('/concerts/1');
});

Route::get('/concerts/{id}', 'ConcertsController@show');
Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');
Route::get('/orders/{confirmationNumber}', 'OrdersController@show');

Route::post('/login', 'Auth\LoginController@login');
