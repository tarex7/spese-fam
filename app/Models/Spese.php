<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spese extends Model
{
    protected $table = 'spese';
    protected $fillable = ['data','importo','categorie_id','tipologia_id','attivo'];

    public function categoria()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }
}
