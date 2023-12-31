<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use App\Models\CategorieSpese;
use App\Models\Tipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpeseController extends Controller
{
    public function index()
    {

        $spese = Spese::select('spese.*')
            ->where('spese.attivo', 1)
            ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
            ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->get();

            //dd($spese)
        ;
        $cat = CategorieSpese::all();
        $cat_opt = array(0 => '--Seleziona--');
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }

        $tip = Tipologia::all();
        $tip_opt = array(0 => '--Seleziona--');
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }

        // dd($spese);
        return view('spese.spese')
            ->with('spese', $spese)
            ->with('cat', $cat_opt)
            ->with('spese_id', null)
            ->with('tip', $tip_opt);
    }


    public function aggiungi(Request $request)
    {

        //dd($request->all());

        if($request->nome != '') {
            $create = Spese::create([

                'nome' => $request->nome_add,
                'data' => date('Y-m-d', strtotime($request->data_add)),
                'importo' => $request->importo_add,
                'categorie_id' => $request->categorie_add,
                'tipologia_id' => $request->tipologia_add,
                'attivo' => 1,
            ]);

            $spese = Spese::select('spese.*')
                ->where('spese.attivo', 1)
                ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
                ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
                ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
                ->get();

            $cat = CategorieSpese::all();
            $cat_opt = array(0 => '--Seleziona--');
            foreach ($cat as $c) {
                $cat_opt[$c->id] = $c->nome;
            }

            $tip = Tipologia::all();
            $tip_opt = array(0 => '--Seleziona--');
            foreach ($tip as $c) {
                $tip_opt[$c->id] = $c->nome;
            }
            return view('spese.spese')
            ->with('success', 'Spesa aggiunta!')
            ->with('spese_id', $create->id)
            ->with('cat', $cat_opt)
            ->with('spese', $spese)
            ->with('tip', $tip_opt)
            ;
        } else {
            return redirect()->back()->with('error','Inserisci un nome valido');
        }

    }

    public function modifica(Request $request)
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
    }



    public function elimina($id)
    {

    Spese::where('id', $id)->update([
            'attivo' => 0,

        ]);

        return redirect()->route('spese')
            ->with('success', 'Spesa eliminata! 😁👍');
    }
}
