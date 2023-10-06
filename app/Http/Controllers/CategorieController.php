<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categorie;

class CategorieController extends Controller
{
    public function index() {
       $categorie = new Categorie();

       $x = $categorie->list();

       
      
    }
}
