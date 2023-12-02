<?php

namespace App\Http\Controllers;

use App\Models\CategorieSpese;
use App\Models\Tipologia;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    // public function index() {
    //    $categorie = new CategorieSpese();

    //    $x = $categorie->list();

    //    return view('categorie.categorie');

    // }

    public function index()
    {

        $categorie = CategorieSpese::select('categorie.*')
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







    public function modifica(Request $request)
    {

        dd($request->all());
        $categorie = $request->categorie;

        foreach ($categorie as $s) {

            CategorieSpese::where('id', $s['id'])
                ->update([
                    'nome' => $s['nome'],
                    'modificato' => date('Y-m-d H:i:s'),
                    'modificatore' => auth()->user()->name,
                ]);
        }
        return redirect()->back()->with('success', 'Modifica salvata!');
    }

    /*  public function modifica(Request $request)
    {

        // dd($request->all());
        $spese = $request->spese;

        foreach ($spese as $s) {

            Spese::where('id', $s['id'])
                ->update([
                    'nome' => $s['nome'],
                    'data' => $s['data'],
                    'importo' => intval($s['importo']),
                    'categorie_id' => $s['categorie_id'],
                    'tipologia_id' => $s['tipologia_id'],
                    'modificato' => date('Y-m-d H:i:s'),
                    'modificatore' => auth()->user()->name,
                ]);
        }
        return redirect()->back()->with('success', 'Modifica salvata!');
    }  */





    public function aggiungi(Request $request)
    {

        dd($request->all());
        if ($request->nome_add != '') {

            $create = CategorieSpese::create([

                'nome' => $request->nome_add,
                'attivo' => 1,
            ]);
            $categorie = new CategorieSpese();
            $categorie->list();


            return view('categorie.categorie')
                ->with('success', 'Categoria aggiunta!')
                ->with('categorie_id', $create->id)
                ->with('categorie', $categorie->get());
        } else {
            return redirect()->back()->with('error', 'Inserisci un nome valido!');
        }
    }










    public function elimina($id)
    {
        //dd($id);
        CategorieSpese::where('id', $id)->update([
            'attivo' => 0,
        ]);

        return redirect()->route('categorie')
            ->with('success', 'Spesa eliminata! 😁👍');
    }
}
