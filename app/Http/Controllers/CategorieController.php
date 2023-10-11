<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Tipologia;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    

    public function index()
    {

        $categorie = categorie::select('categorie.*')
            ->where('categorie.attivo', 1)
            ->select('categorie.*')
            ->get();

            //dd($categorie)
        ;


        // dd($categorie);
        return view('categorie.categorie')
            ->with('categorie', $categorie)
            ->with('categorie_id', null);
    }







    public function aggiungi(Request $request)
    {

         //dd($request->all());
        $categorie = Categorie::where('attivo', 1)->get();

        // dd($categorie);
        if ($request->nome_add != '') {
            Categorie::create([
                'nome' => $request->nome_add,
                'attivo' => 1,
                'creato' => date('Y-m-d H:i:s'),
                'creatore' => auth()->user()->name,
            ]);

            return redirect()->back()->with('success', 'Categoria aggiunta!');
        } else {
            return redirect()->route('categorie')->with('error', 'Inserisci un nome valido!');
        }
    }







    public function salva(Request $request)
    {

        //  dd($request->all());



        foreach ($request->categorie as $k => $c) {

            Categorie::where('id', $k)->update([
                'nome' => $c,
                'modificatore' => auth()->user()->name,
                'modificato' => date('Y-m-d'),

            ]);
        }

        $categorie = Categorie::where('attivo', 1)->get();


        return view('categorie.categorie')
            ->with('success', 'Categoria aggiunta!')
            ->with('categorie', $categorie)
            ;
    }










    public function elimina($id)
    {
        //dd($id);
        Categorie::where('id', $id)->update([
            'attivo' => 0,
        ]);

        return redirect()->route('categorie')
            ->with('success', 'Categoria eliminata! 😁👍');
    }
}
