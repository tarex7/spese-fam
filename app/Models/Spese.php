<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spese extends Model
{
    protected $table = 'spese';
    protected $fillable = ['nome','data','importo','categorie_id','tipologia_id','attivo'];

    const UPDATED_AT = 'modificato';
    const CREATED_AT = 'creato';

    public function categoria()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public static function getCategorieOptions() {
        $cat = Categorie::where('attivo', 1)->orderBy('nome', 'ASC')->get();
        $cat_opt = ['0' => '--Seleziona--'];
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }
        return $cat_opt;
    }

    public static function getTipologiaOptions() {
        $tip = Tipologia::all();
        $tip_opt = ['0' => '--Seleziona--'];
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }
        return $tip_opt;
    }


}
