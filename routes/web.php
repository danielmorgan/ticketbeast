<?php

Route::get('/mockups/order', function() {
    return view('order');
});

Route::get('/concerts/{id}', 'ConcertsController@show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');

Route::get('/orders/{confirmationNumber}', 'OrdersController@show');
