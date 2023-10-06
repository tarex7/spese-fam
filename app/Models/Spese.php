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
}
