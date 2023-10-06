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
    public function index()
    {
        $result = DB::table('tipologia')->where('nome' , "LIKE", '%ET')->get();
        dd($result);

      /*  $result = DB::table('tipologia')
    ->whereRaw("LOWER(nome) COLLATE utf8mb4_general_ci LIKE ?", ['et'])
    ->get();*/

    

        $spese = Spese::select('spese.*')
            ->where('attivo', 1)
            ->leftJoin('categorie', 'spese.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'spese.tipologia_id', 'tipologia.id')
            ->select('spese.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->get();

            // dd($spese)
        ;
        $cat = Categorie::all();
        $cat_opt = array();
        foreach ($cat as $c) {
            $cat_opt[$c->id] = $c->nome;
        }

        $tip = Tipologia::all();
        $tip_opt = array();
        foreach ($tip as $c) {
            $tip_opt[$c->id] = $c->nome;
        }



        // dd($spese);
        return view('spese.spese')
            ->with('spese', $spese)
            ->with('cat', $cat_opt)
            ->with('tip', $tip_opt);
    }

    public function nuovo()
    {
    }

    public function salva(Request $request)
    {
        //dd($request->all());
        foreach ($request->spese as $k => $sp) {
            //dd( $request->spese[$k]);
            Spese::where('id', $request->spese[$k])->update([
                'data' => $request->data[$k],
                'importo' => $request->importo[$k],
                'categorie_id' => $request->categorie[$k],
                'tipologia_id' => $request->tipologia[$k],
                'test' =>  $k
            ]);
        }
        return redirect()->back();
    }

    public function aggiungi(Request $request)
    {

       // Definisci le regole di validazione
    $rules = [
        'data_add' => 'required|date',
        'importo_add' => 'required|numeric',
        'categorie_add' => 'required',
        'tipologia_add' => 'required',
    ];

    // Definisci i messaggi di errore personalizzati
    $messages = [
        'required' => 'Il campo :attribute è obbligatorio.',
        'date' => 'Il campo :attribute deve essere una data valida.',
        'numeric' => 'Il campo :attribute deve essere un valore numerico.',
    ];

    // Esegui la validazione
    $validator = Validator::make($request->all(), $rules, $messages);

    // Verifica se ci sono errori di validazione
    if ($validator->fails()) {
        return redirect()->back()
            ->with('errors','errore') // Passa gli errori alla vista
            ->withInput(); // Mantieni i dati inseriti dall'utente nei campi del modulo
    }

        Spese::create([

            'data' => date('Y-m-d', strtotime($request->data_add)),
            'importo' => $request->importo_add,
            'categorie_id' => $request->categorie_add,
            'tipologia_id' => $request->tipologia_add,
            'attivo' => 1,
        ]);



        return redirect()->back()->with('success', 'ok');
    }
}