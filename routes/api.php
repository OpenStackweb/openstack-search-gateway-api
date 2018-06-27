<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
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

Route::group([
    'prefix'     => 'public/v1',
    'before'     => [],
    'after'      => [],
    'middleware' => [
    ]
], function(){
    Route::get('search/{context}/{term}', 'SearchApiController@doSearch');
    Route::get('suggestions/{context}/{term}', 'SearchApiController@getSuggestions');
});