<?php

use Illuminate\Support\Facades\Route;
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
Route::get('/', static function () {
    return view('welcome');
});

//文档  暂定
Route::get('/help', static function () {
    return redirect()->away('https://1m29yvnp67.k.topthink.com/@xuriqbot');
});

Route::get('/text/{key}', static function (string $key) {
    return view('text',[
        'key'=>$key
    ]);
});
