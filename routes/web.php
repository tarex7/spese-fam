<?php

use Illuminate\Support\Facades\Auth;
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

Auth::routes();

Route::get('/dashboard', 'DashboardController@index')->middleware('auth');

Route::middleware(['auth'])->group(function () {

    Route::get('/', 'SpeseController@index')->name('spese');

    // Route::any('/spese/salva', 'SpeseController@salva')->name('spese');
    Route::any('/spese/salva', 'SpeseController@salva')->name('spese/salva');
    Route::any('/spese/aggiungi', 'SpeseController@aggiungi')->name('spese/aggiungi');
    Route::any('/spese/elimina/{id}', 'SpeseController@elimina')->name('spese/elimina');

    //CATEGORIE
    Route::get('/categorie', 'CategorieController@index')->name('categorie');
    Route::any('/categorie/elimina/{id}', 'categorieController@elimina')->name('categorie/elimina');
    Route::any('/categorie/salva', 'CategorieController@salva')->name('categorie/salva');

    Route::any('/categorie/aggiungi', 'CategorieController@aggiungi')->name('categorie/aggiungi');

    //TIPOLOGIA
    Route::get('/tipologia', 'TipologiaController@index')->name('tipologia');
    //Route::get('/spese/nuovo', 'SpeseController@nuovo')->name('spese/nuovo');


    // Route::get('/home', 'HomeController@index')->name('home');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
