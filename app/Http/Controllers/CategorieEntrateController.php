<?php

namespace App\Http\Controllers;

use App\Models\CategorieEntrate;
use Illuminate\Http\Request;

class CategorieEntrateController extends Controller
{
    public function index()
    {

        $categorie_entrate = CategorieEntrate::select('categorie_entrate.*')
            ->where('categorie_entrate.attivo', 1)
            ->select('categorie_entrate.*')
            ->get();;


        // dd($categorie_entrate);
        return view('categorie_entrate.categorie_entrate')
            ->with('categorie_entrate', $categorie_entrate);
           // ->with('categorie_entrate_id', null);
    }

    public function elenco()
    {
        $categorie = CategorieEntrate::select('categorie.*')
            ->where('categorie_entrate.attivo', 1)
            ->select('categorie_entrate.*')
            ->get();

        return $categorie;;
    }


    public function aggiungi($nome)
    {

        //dd($request->all());

        // dd($categorie);
        if ($nome != '') {
            CategorieEntrate::create([
                'nome' => $nome,
                'attivo' => 1,
                'creato' => date('Y-m-d H:i:s'),
                'creatore' => auth()->user()->name,
            ]);

            // return view('categorie_entrate.categorie')

            //     ->with('categorie_id', null);
        }
    }



    public function salva(Request $request)
    {

        //  dd($request->all());


        foreach ($request->categorie_entrate as $k => $c) {

            CategorieEntrate::where('id', $k)->update([
                'nome' => $c,
                'modificatore' => auth()->user()->name,
                'modificato' => date('Y-m-d'),

            ]);
        }

        $categorie_entrate = CategorieEntrate::where('attivo', 1)->get();


        return view('categorie_entrate.categorie_entrate')
            ->with('success', 'Categoria aggiunta!')
            ->with('categorie_entrate', $categorie_entrate);
    }


    public function elimina($id)
    {
        //dd($id);
        CategorieEntrate::where('id', $id)->update([
            'attivo' => 0,
        ]);

        
    }
}
