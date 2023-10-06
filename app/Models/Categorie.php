<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Categorie extends Model
{
   protected $table = 'categorie';

   

   public function list() {

    $list = DB::table('categorie')->select('categorie.*')->get();

   
   }
}
