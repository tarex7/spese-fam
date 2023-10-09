<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use App\Models\Categorie;
use App\Models\Tipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpeseController extends Controller
{
    public function index($month = null)
    {

       if($month == null) {
        $spese = Spese::select('spese.*')
        ->where('spese.attivo', 1)
        ->whereMonth('data', '=', date('n'))
        ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
        ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
        ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
        ->get();

        //dd($spese)
    ;
       } else {
        $spese = Spese::select('spese.*')
        ->where('spese.attivo', 1)
        ->whereMonth('data', '=', $month)
        ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
        ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
        ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
        ->get();

        //dd($spese)
    ;
       }


        $cat = Categorie::all();
        $cat_opt = array(0 => '--Seleziona--');
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }

        $tip = Tipologia::all();
        $tip_opt = array(0 => '--Seleziona--');
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }

        $mesi = [
           ' 0' => 'Anno',
            '1' => 'Gennaio',
            '2' => 'Febbraio',
            '3' => 'Marzo',
            '4' => 'Aprile',
            '5' => 'Maggio',
            '6' => 'Giugno',
            '7' => 'Luglio',
            '8' => 'Agosto',
            '9' => 'Settembre',
            '10' => 'Ottobre',
            '11' => 'Novembre',
            '12' => 'Dicembre'
        ];
        

        // dd($spese);
        return view('spese.spese')
            ->with('spese', $spese)
            ->with('mesi', $mesi)
            ->with('cat', $cat_opt)
            ->with('spese_id', null)
            ->with('tip', $tip_opt);
    }


    public function aggiungi(Request $request)
    {

        //dd($request->all());

        if ($request->nome_add != '') {
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

            $cat = Categorie::all();
            $cat_opt = array(0 => '--Seleziona--');
            foreach ($cat as $c) {
                $cat_opt[$c->id] = $c->nome;
            }

            $tip = Tipologia::all();
            $tip_opt = array(0 => '--Seleziona--');
            foreach ($tip as $c) {
                $tip_opt[$c->id] = $c->nome;
            }

            return redirect()->route('spese')
                ->with('success', 'Spesa aggiunta!')
                ->with('spese_id', $create->id)
                ->with('cat', $cat_opt)
                ->with('spese', $spese)
                ->with('tip', $tip_opt);
        } else {
            return redirect()->back()->with('error', 'Inserisci un nome valido');
        }
    }

    public function salva(Request $request)
    {

      
        foreach ($request->spese as $k => $s) {
           
            Spese::where('id', $k)
                ->update([
                    'nome' => $s['nome'],
                    'data' => $s['data'],
                    'importo' => intval($s['importo']),
                    'categorie_id' => $s['categorie'],
                    'tipologia_id' => $s['tipologia'],
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

    public function filtra() {
        
    }
}
