<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

////前台路由组
//Route::group(['namespace' => 'Home'], function(){
//    // 控制器在 "App\Http\Controllers\Home" 命名空间下
//
//    Route::get('bind/view-bind-area', 'BindController@viewBindArea');
//    Route::any('bind/bind-area', 'BindController@bindArea');
//    Route::get('bind/view-bind-account', 'BindController@viewBindAccount');
//    Route::any('bind/bind', 'BindController@bind');
//
//    Route::any('login/login', 'LoginController@login');
//    Route::any('login/logout', 'LoginController@logout');
//
//});
//
////后台路由组
//Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function(){
//    // 控制器在 "App\Http\Controllers\Admin" 命名空间下
//    Route::any('login/login', 'LoginController@login');
//    Route::any('login/logout', 'LoginController@logout');
//
//    Route::get('ambass/lists', 'AmbassController@lists');
//    Route::get('ambass/self-profit', 'AmbassController@selfProfit');
//    Route::get('ambass/view-add-ambass', 'AmbassController@viewAddAmbass');
//    Route::any('ambass/add-ambass', 'AmbassController@addAmbass');
//
//});
