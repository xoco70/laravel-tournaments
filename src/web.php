<?php

Route::group(['middleware' => ['web']], function () {
    Route::get('treeDemo', 'Xoco70\LaravelTournaments\TreeDemoController@index')->name('tree_demo.index');
});


