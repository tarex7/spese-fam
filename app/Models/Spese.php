<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Spese extends Model
{
    protected $table = 'spese';
    protected $fillable = ['nome', 'data', 'importo', 'categorie_id', 'tipologia_id', 'attivo'];

    const UPDATED_AT = 'modificato';
    const CREATED_AT = 'creato';

    public function categoria()
    {
        return $this->belongsTo(CategorieSpese::class, 'categorie_id');
    }

    public function tipologia()
    {
        return $this->belongsTo(Tipologia::class, 'tipologia_id');
    }

    public static function getCategorieOptions()
    {
        $cat = CategorieSpese::where('attivo', 1)->orderBy('nome', 'ASC')->get();
        $cat_opt = [];
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }
        return $cat_opt;
    }

    public static function getTipologiaOptions()
    {
        $tip = Tipologia::all();
        $tip_opt = [];
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }
        return $tip_opt;
    }

    public static function creaDaRichiesta($request)
    {
        return static::create([
            'nome' => $request->nome_add,
            'data' => date('Y-m-d', strtotime($request->data_add)),
            'importo' => $request->importo_add,
            'categorie_id' => $request->categorie_add,
            'tipologia_id' => $request->tipologia_add,
            'attivo' => 1,
            'creatore' => Auth::user()->name,
            'creato' => date('Y-m-d'),
        ]);
    }

    public static function getYearsOptions()
    {
        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);

        $years = [0 => 'Seleziona'];

        foreach ($anni as $a) {
            $years[$a] = $a;
        }
        return $years;
    }

    public static function getMesiOptions()
    {
        $mesi = [
            ' 0' => '--Seleziona--',
            '1' => 'Gennaio',
            '2' => 'Febbraio',
            '3' => 'Marzo',
            '4' => 'Aprile',
            '5' => 'Maggio',
            '6' => 'Giugno',
            '7' => 'Luglio',
            '8' => 'Agosto',
            '9' => 'Settembre',
            '10' => 'Ottobre',
            '11' => 'Novembre',
            '12' => 'Dicembre'
        ];

        return $mesi;
    }




}
