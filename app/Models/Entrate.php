<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrate extends Model
{
    protected $table = 'entrate';
    protected $fillable = ['nome','data','importo','categorieentrate_id','tipologia_id','attivo'];

    const UPDATED_AT = 'modificato';
    const CREATED_AT = 'creato';

    public function categoria_entrate()
    {
        return $this->belongsTo(CategorieEntrate::class, 'categorieentrate_id');
    }
}
