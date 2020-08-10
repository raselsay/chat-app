<?php

use App\User;
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

Route::get('/', function () {
	 $users =  User::all();
    return view('welcome',compact('users'));
});

Route::get('t/users/{user}','messageController@index')->name('t');

Route::post('/conversations/{conversation}','messageController@sendMessage')->name('sender');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
