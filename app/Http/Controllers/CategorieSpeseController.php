<?php

namespace App\Http\Controllers;

use App\Models\CategorieSpese;
use App\Models\Tipologia;
use Illuminate\Http\Request;

class CategorieSpeseController extends Controller
{


    public function index()
    {

        return view('categorie_spese.categorie')

            ->with('categorie_id', null);
    }

    public function elenco()
    {
        $categorie = CategorieSpese::select('categorie.*')
            ->where('categorie_spese.attivo', 1)
            ->select('categorie_spese.*')
            ->get();

        return $categorie;;
    }


    public function aggiungi($nome)
    {

        //dd($request->all());

        // dd($categorie);
        if ($nome != '') {
            CategorieSpese::create([
                'nome' => $nome,
                'attivo' => 1,
                'creato' => date('Y-m-d H:i:s'),
                'creatore' => auth()->user()->name,
            ]);

            return view('categorie_spese.categorie')

                ->with('categorie_id', null);
        }
    }







    public function salva(Request $request)
    {

        //  dd($request->all());



        foreach ($request->categorie as $k => $c) {

            CategorieSpese::where('id', $k)->update([
                'nome' => $c,
                'modificatore' => auth()->user()->name,
                'modificato' => date('Y-m-d'),

            ]);
        }

        $categorie = CategorieSpese::where('attivo', 1)->get();


        return view('categorie_spese.categorie')
            ->with('success', 'Categoria aggiunta!')
            ->with('categorie', $categorie);
    }



    public function elimina($id)
    {
        //dd($id);
        CategorieSpese::where('id', $id)->update([
            'attivo' => 0,
        ]);

        return redirect()->route('categorie')
            ->with('success', 'Categoria eliminata! 😁👍');
    }
}
