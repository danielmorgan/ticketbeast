<?php

Route::get('/mockups/order', function() {
    return view('orders.show');
});

Route::get('/concerts/{id}', 'ConcertsController@show');

Route::post('/concerts/{id}/orders', 'ConcertOrdersController@store');
