<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('/kendo-tournaments', 'Xoco70\KendoTournaments\TreeDemoController@index')->name('tree_demo.index');
});


