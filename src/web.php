<?php

Route::group(['prefix' => 'kendo-tournaments', 'middleware' => ['web']], function () {
    Route::get('/', 'Xoco70\KendoTournaments\TreeController@index')->name('tree.index');
    Route::post('/championships/{championship}/trees', 'Xoco70\KendoTournaments\TreeController@store')->name('tree.index');
});


