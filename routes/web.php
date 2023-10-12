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


    //SPESE
    Route::get('/', 'SpeseController@index')->name('spese');
    Route::any('/spese/salva', 'SpeseController@salva')->name('spese/salva');
    Route::any('/spese/aggiungi', 'SpeseController@aggiungi')->name('spese/aggiungi');
    Route::any('/spese/elimina/{id}', 'SpeseController@elimina')->name('spese/elimina');
    Route::any('/spese/filtra/{month?}', 'SpeseController@filtra')->name('spese/filtra');
    Route::any('/spese/elenco', 'SpeseController@elenco')->name('spese/elenco');

    //ENTRATE
    Route::get('entrate', 'EntrateController@index')->name('entrate');
    Route::any('/entrate/salva', 'EntrateController@salva')->name('entrate/salva');
    Route::any('/entrate/aggiungi', 'EntrateController@aggiungi')->name('entrate/aggiungi');
    Route::any('/entrate/elimina/{id}', 'EntrateController@elimina')->name('entrate/elimina');
    Route::any('/entrate/filtra/{month?}', 'EntrateController@filtra')->name('entrate/filtra');

    //CATEGORIE
    Route::get('/categorie', 'CategorieController@index')->name('categorie');
    Route::any('/categorie/elimina/{id}', 'categorieController@elimina')->name('categorie/elimina');
    Route::any('/categorie/salva', 'CategorieController@salva')->name('categorie/salva');
    Route::any('/categorie/aggiungi', 'CategorieController@aggiungi')->name('categorie/aggiungi');

    //CATEGORIE ENTRATE
    Route::get('/categorie_entrate', 'CategorieEntrateController@index')->name('categorie_entrate');
    Route::any('/categorie_entrate/elimina/{id}', 'CategorieEntrateController@elimina')->name('categorie_entrate/elimina');
    Route::any('/categorie_entrate/salva', 'CategorieEntrateController@salva')->name('categorie_entrate/salva');
    Route::any('/categorie_entrate/aggiungi', 'CategorieEntrateController@aggiungi')->name('categorie_entrate/aggiungi');


    //TIPOLOGIA
    Route::get('/tipologia', 'TipologiaController@index')->name('tipologia');
});



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
