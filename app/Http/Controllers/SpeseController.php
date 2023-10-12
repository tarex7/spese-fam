<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use App\Models\Categorie;
use App\Models\Tipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpeseController extends Controller
{
    public function index()
    {


        $spese = Spese::select('spese.*')
            ->where('spese.attivo', 1)
            // ->whereMonth('data', '=', date('n'))
            ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
            ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->get();

        $totale = $spese->sum('importo'); //dd($spese)
        ;


        $cat = Categorie::where('attivo', 1)->orderBy('nome', 'ASC')->get();
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
            ' 0' => '--Seleziona--',
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

        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);
        $years = [0 => 'Seleziona'];

        $anno_sel = date('Y');
        $mese_sel = date('n');

        foreach ($anni as $key => $a) {
            $years[$a] = $a;
        }

        // dd($spese);
        return view('spese.spese')
            ->with('spese', $spese)
            ->with('mesi', $mesi)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('years', $years)
            ->with('cat', $cat_opt)
            ->with('spese_id', null)
            ->with('totale', $totale)
            ->with('tip', $tip_opt);
    }


    public function aggiungi(Request $request)
    {

        //dd($request->all());



        $create = Spese::create([

            'nome' => $request->nome_add,
            'data' => date('Y-m-d', strtotime($request->data_add)),
            'importo' => $request->importo_add,
            'categorie_id' => $request->categorie_add,
            'tipologia_id' => $request->tipologia_add,
            'attivo' => 1,
            'creatore' => auth()->user()->name,
            'creato' => date('Y-m-d'),
        ]);

        $spese = Spese::select('spese.*')
            ->where('spese.attivo', 1)
            ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
            ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->get();

        $totale = $spese->sum('importo');


        $cat = Categorie::where('attivo', 1)->orderBy('nome', 'ASC')->get();
        $cat_opt = array(0 => '--Seleziona--');
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }

        $tip = Tipologia::all();
        $tip_opt = array(0 => '--Seleziona--');
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }

        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);
        $years = [0 => 'Seleziona'];
        foreach ($anni as $key => $a) {
            $years[$a] = $a;
        }
        $anno_sel = date('Y');
        $mese_sel = date('n');

        $mesi = [
            ' 0' => '--Seleziona--',
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

        //dd($create->id);
        return view('spese.spese')
            ->with('success', 'Spesa aggiunta!')
            ->with('cat', $cat_opt)
            ->with('spese', $spese)
            ->with('years', $years)
            ->with('mesi', $mesi)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('spese_id', $create->id)
            ->with('totale', $totale)
            ->with('tip', $tip_opt);
    }


    public function salva(Request $request)
    {

        //dd($request->all());
        foreach ($request->spese as $k => $s) {

            Spese::where('id', $k)
                ->update([
                    // 'nome' => $s['nome'],
                    'data' => $s['data'],
                    'importo' => $s['importo'],
                    'categorie_id' => $s['categorie'],
                    'tipologia_id' => $s['tipologia'],
                    'modificato' => date('Y-m-d H:i:s'),
                    'modificatore' => auth()->user()->name,
                ]);
        }
        return redirect()->route('spese')->with('success', 'Modifica salvata!');
    }


    public function elimina($id)
    {

        Spese::where('id', $id)->update([
            'attivo' => 0,

        ]);

        return redirect()->route('spese')
            ->with('success', 'Spesa eliminata! 😁👍');
    }


    public function filtra(Request $request)
    {

        //dd($request->all());
        $mese = $request->mese;
        $anno = $request->anno;

        $spese = Spese::select('spese.*')
            ->where('spese.attivo', 1)
            ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
            ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia');


        if ($mese != '') {

            $spese->whereMonth('data', '=', $mese);
            $mese_sel = $mese;
        }


        if ($anno != '0') {

            $spese->whereYear('data', '=', $anno);

            $anno_sel = $anno;
            // dd($anno_sel);
        }

        $ris = $spese->paginate();

        $totale = $ris->sum('importo');


        //dd($spese);

        $cat = Categorie::where('attivo', 1)->orderBy('nome', 'ASC')->get();
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
            ' 0' => '--Seleziona--',
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

        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);
        $years = [0 => 'Seleziona'];

        // $anno_sel = date('Y');
        // $mese_sel = date('n');

        foreach ($anni as $key => $a) {
            $years[$a] = $a;
        }

        // dd($ris);
        return view('spese.spese')
            ->with('spese', $ris)
            ->with('mesi', $mesi)
            ->with('years', $years)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('cat', $cat_opt)
            ->with('totale', $totale)
            ->with('spese_id', null)
            ->with('tip', $tip_opt);
    }





    public function elenco()
    {

        $spesePerCategoria = Spese::join('categorie', 'spese.categorie_id', '=', 'categorie.id')
            ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie.id', 'mese')
            ->get();

        // dd($spesePerCategoria);
        $speseRaggruppate = [];

        foreach ($spesePerCategoria as $spesa) {

            $categoria = $spesa->categoria;
            $mese = $spesa->mese;
            $importo = $spesa->importo;

            if (!isset($speseRaggruppate[$categoria])) {
                $speseRaggruppate[$categoria] = [];
            }

            $speseRaggruppate[$categoria][$mese] = $importo;
        }

        dd($speseRaggruppate);

        return view('spese.elenco')->with('speseRaggruppate', $speseRaggruppate);
    }
}
