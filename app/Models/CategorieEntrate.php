<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategorieEntrate extends Model
{
    const UPDATED_AT = 'modificato';
    const CREATED_AT = 'creato';

   protected $table = 'categorie_entrate';
   protected $fillable = [
      'nome', 'attivo', 
  ];
}
