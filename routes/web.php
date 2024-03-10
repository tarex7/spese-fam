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
    Route::redirect('/', '/spese');

    Route::any('/spese/salva', 'SpeseController@salva')->name('spese/salva');
    Route::any('/spese/aggiungi', 'SpeseController@aggiungi')->name('spese/aggiungi');
    Route::any('/spese/elimina/{id}', 'SpeseController@elimina')->name('spese/elimina');
    Route::any('/spese/filtra/{month?}', 'SpeseController@filtra')->name('spese/filtra');
    Route::any('/spese/elenco/{year?}', 'SpeseController@elenco')->name('spese/elenco');
    Route::any('/spese/getelenco/{year?}', 'SpeseController@getElenco')->name('spese/getelenco');
    Route::any('/spese/importa', 'SpeseController@importa')->name('spese/importa');
    Route::any('/spese/carica_file', 'SpeseController@carica_file')->name('spese/carica_file');
    Route::get('/spese/{anno?}', 'SpeseController@index')->name('spese');
    Route::any('/spese/spesemensili/{anno?}', 'SpeseController@calcolaSpeseMensili')->name('spese/spesemensili');

    //ENTRATE
    Route::any('/entrate/salva', 'EntrateController@salva')->name('entrate/salva');
    Route::any('/entrate/aggiungi', 'EntrateController@aggiungi')->name('entrate/aggiungi');
    Route::any('/entrate/elimina/{id}', 'EntrateController@elimina')->name('entrate/elimina');
    Route::any('/entrate/filtra/{month?}', 'EntrateController@filtra')->name('entrate/filtra');
    Route::any('/entrate/elenco/{year?}', 'EntrateController@elenco')->name('entrate/elenco');
    Route::any('/entrate/getelenco/{year?}', 'EntrateController@getElenco')->name('entrate/getelenco');

    Route::any('/entrate/importa', 'EntrateController@importa')->name('entrate/importa');
    Route::any('/entrate/carica_file', 'EntrateController@carica_file')->name('entrate/carica_file');
    Route::get('entrate/{anno?}', 'EntrateController@index')->name('entrate');
    Route::any('/entrate/entratemensili/{anno?}', 'EntrateController@calcolaentrateMensili')->name('entrate/entratemensili');

    //CATEGORIE
    Route::any('/categorie/spese', 'CategorieSpeseController@index')->name('categorie');
    Route::any('/categorie/spese/elenco', 'CategorieSpeseController@elenco')->name('categorie/spese/elenco');
    Route::any('/categorie/spese/elimina/{id}', 'categorieSpeseController@elimina')->name('categorie/elimina');
    Route::any('/categorie/salva', 'CategorieSpeseController@salva')->name('categorie/salva');
    Route::any('/categorie/spese/aggiungi/{nome}', 'CategorieSpeseController@aggiungi')->name('categorie/aggiungi');

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
