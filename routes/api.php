<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::namespace('App\Http\Controllers')->group(function(){
    Route::group(['middleware'=>'api','prefix'=>'auth'],function(){
        Route::post('authenticate','AuthController@authenticate')->name('api.authenticate');
        Route::post('register','AuthController@register')->name('api.register');
    });

    Route::group(['middleware' => ['api','auth'],'prefix' => 'recipe'],function (){
        Route::post('create','RecipeController@create')->name('recipe.create');
        Route::get('all','RecipeController@all')->name('recipe.all');
        Route::post('update/{recipe}','RecipeController@update')->name('recipe.update');
        Route::get('show/{recipe}','RecipeController@show')->name('recipe.show');
        Route::post('delete/{recipe}','RecipeController@delete')->name('recipe.delete');
    });

    Route::post('transfer-balance','WalletController@transferBalance');
    Route::get('/sequential', 'ConcurrentController@sequential');
    Route::get('/concurrent', 'ConcurrentController@concurrent');
    Route::get('/ownapi', 'ConcurrentController@ownapi');

    /** API Logs */
    Route::get('/logs/get','LogsController@index')->middleware('log.route');
    Route::post('/logs/post','LogsController@postMethod')->middleware('log.route');
});

