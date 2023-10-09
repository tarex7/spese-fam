<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Categorie extends Model
{

   const UPDATED_AT = 'modificato';
    const CREATED_AT = 'creato';

   protected $table = 'categorie';
   protected $fillable = [
      'nome', 'attivo', 
  ];


   public function list()
   {

      $list = DB::table('categorie')->where('attivo',1)->select('categorie.*')->get();
    
      return $list;
   }
}
