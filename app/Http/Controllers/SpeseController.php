<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\AddSpesaRequest;
use App\Http\Requests\EditSpesaRequest;

class SpeseController extends Controller
{

    private function SpeseQuery()
    {
        return Spese::with(['categoria', 'tipologia'])
            ->where('attivo', 1);
    }

    public function getYearsOptions() {
        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);

        $years = [0 => 'Seleziona'];

        foreach ($anni as $a) {
            $years[$a] = $a;
        }
        return $years;
    }

    public function getMesiOptions() {
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

        return $mesi;
    }

    public function index()
    {
        $now = Carbon::now();

        $anno_sel = $now->year;
        $mese_sel = $now->month;

        $spese = $this->SpeseQuery()
            ->whereMonth('data', '=', date('n'))
            ->whereYear('data', '=', date('Y'))
            ->paginate(10);

        $totale = $spese->sum('importo');

        return view('spese.spese')
            ->with('spese', $spese)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('years', $this->getYearsOptions())
            ->with('mesi', $this->getMesiOptions())
            ->with('cat', Spese::getCategorieOptions())
            ->with('tip', Spese::getTipologiaOptions())
            ->with('spese_id', null)
            ->with('totale', $totale);
    }


    public function aggiungi(AddSpesaRequest $request)
    {
        Spese::creaDaRichiesta($request);
        return redirect()->route('spese')->with('success','Spesa Aggiunta 👍');
    }


    public function salva(EditSpesaRequest $request)
    {

     // dd($request->all());
        foreach ($request->spese as $k => $s) {

            Spese::where('id', $k)
                ->update([
                    // 'nome' => $s['nome'],
                    'data' => $s['data'],
                    'importo' => $s['importo'],
                    'categorie_id' => $s['categorie'],
                    'tipologia_id' => $s['tipologia'],
                    'modificato' => now(),
                    'modificatore' => Auth::user()->name,
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
        //  dd($request->all());
        $mese = $request->mese ?? 0;
        $anno = $request->anno ?? 0;

        $spese = $this->SpeseQuery()
            ->when($mese != 0, function ($query) use ($mese) {
                return $query->whereMonth('data', '=', $mese);
            })
            ->when($anno != 0, function ($query) use ($anno) {
                return $query->whereYear('data', '=', $anno);
            });


        $totale = $spese->sum('importo');

        $ris = $spese->paginate(10)->appends($request->query());

        return view('spese.spese')
            ->with('spese', $ris)
            ->with('mesi' , $this->getMesiOptions())
            ->with('years', $this->getYearsOptions())
            ->with('cat', Spese::getCategorieOptions())
            ->with('tip', Spese::getTipologiaOptions())
            ->with('anno', $anno)
            ->with('mese', $mese)
            ->with('totale', $totale)
            ->with('spese_id', null);
    }

    public function calcolaSpeseMensili($year) {
        return Spese::where('attivo', 1)
            ->whereYear('data', $year)
            ->get()
            ->groupBy(function($data) {
                return Carbon::parse($data->data)->format('m'); // raggruppa per mese
            })
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item->sum('importo')]; // calcola la somma per ogni mese
            });
    }



    public function elenco(Request $request)
    {
        //dd($request->all());

        // Ottiene l'anno dalla request o usa l'anno corrente
        $year = $request->anno ?? date('Y');

        $speseMensili = Spese::where('attivo', 1)
            ->whereYear('data', $year)
            ->groupBy(DB::raw('MONTH(data)'))
            ->select(DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as totale'))
            ->pluck('totale', 'mese');


        $spesePerCategoria = Spese::join('categorie', 'spese.categorie_id', '=', 'categorie.id')
            ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie.nome', 'mese')
            ->whereYear('data', $year)
            ->get();

        $years = range(date('Y') - 10, date('Y') + 10);
        $years = [0 => 'Seleziona'] + array_combine($years, $years);

        $speseRaggruppate = $spesePerCategoria->groupBy('categoria')
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item->pluck('importo', 'mese')->all() + array_fill(1, 12, 0)];
            });

        return view('spese.elenco', [
            'years' => $years,
            'anno_sel' => $year,
            'speseRaggruppate' => $speseRaggruppate,
            'spese_mensili' => $speseMensili->toArray()
        ]);
    }




    public function importa()
    {
        return view('spese/importa');
    }


    public function carica_file(Request $request)
    {

        $anno = $request->anno;
        // $request->validate([
        //     'excel_file' => 'required|mimes:xls,xlsx',
        // ]);

        $file = $request->file('excel_file');

        // Carica il file Excel utilizzando Maatwebsite/Excel
        $data = Excel::toCollection([], $file);

        //dd($data);

        foreach ($data as $sheet) {
            //dd($sheet);

            foreach ($sheet as $row) {
                // dd($row);

                for ($i = 1; $i < count($row); $i++) {

                    if ($row[$i] == null) {
                        //dd($row[$i]);
                        $row[$i] = 0.00;
                    }

                    // dd($row);
                    switch ($i) {
                        case 1:
                            $mese = "01";
                            break;
                        case 2:
                            $mese = "02";
                            break;
                        case 3:
                            $mese = "03";
                            break;
                        case 4:
                            $mese = "04";
                            break;
                        case 5:
                            $mese = "05";
                            break;
                        case 6:
                            $mese = "06";
                            break;
                        case 7:
                            $mese = "07";
                            break;
                        case 8:
                            $mese = "08";
                            break;
                        case 9:
                            $mese = "09";
                            break;
                        case 10:
                            $mese = "10";
                            break;
                        case 11:
                            $mese = "11";
                            break;
                        case 12:
                            $mese = "12";
                            break;
                    }

                    //dd($row[$i]);
                    $categoria = strtolower($row[0]);

                    $categorie_esistenti = array();
                    $categorie_esistenti_query = Categorie::where('attivo', 1)->pluck('nome');
                    foreach ($categorie_esistenti_query as $c) {
                        array_push($categorie_esistenti, $c);
                    }

                    // dd($categorie_esistenti);

                    $cat_id = null;
                    if (!in_array($categoria, $categorie_esistenti)) {
                        $cat = Categorie::create([
                            'nome' => strtolower($categoria),
                            'attivo' => 1,
                            'creatore' => Auth::user()->name,
                            'creato' => date('Y-m-d'),
                        ]);
                        $cat_id = $cat->id;
                    } else {
                        $cat_id = Categorie::where('nome', $categoria)->first()->id;
                        //dd($cat_id);
                    }

                    //dd($cat_id);

                    Spese::create([
                        'nome' => $categoria,
                        'importo' => $row[$i],
                        'categorie_id' => $cat_id,
                        'data' => $anno . '-' . $mese . '-01',
                        'attivo' => 1,
                        'creatore' => Auth::user()->name,
                        'creato' => date('Y-m-d'),
                    ]);
                }
                //dd($row);

            }
        }

        // Il risultato è una collezione di fogli Excel, uno per ogni foglio nel file
        // $firstSheet = $data->first();

        // Ora puoi lavorare con i dati Excel utilizzando le collezioni Laravel
        // if ($firstSheet instanceof Collection) {
        //     $headerRow = $firstSheet->shift();

        //     // Esempio: stampa l'header e le prime 5 righe di dati
        //     dd($headerRow, $firstSheet->take(5));
        // }

        return redirect()->route('spese/importa')->with('success', 'File Excel elaborato con successo');
    }
}

/*
public function carica_file(Request $request)
{
    $anno = $request->anno;
    $request->validate([
        'excel_file' => 'required|mimes:xls,xlsx',
    ]);

    $file = $request->file('excel_file');
    $data = Excel::toCollection([], $file);

    $categorieEsistenti = Categorie::where('attivo', 1)->pluck('nome')->all();
    $mesi = array_flip(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']);

    foreach ($data as $sheet) {
        foreach ($sheet as $row) {
            for ($i = 1; $i < count($row); $i++) {
                $row[$i] = $row[$i] ?? 0.00;
                $mese = $mesi[$i] ?? '01';
                $categoria = strtolower($row[0]);

                if (!in_array($categoria, $categorieEsistenti)) {
                    $cat = Categorie::create([
                        'nome' => $categoria,
                        'attivo' => 1,
                        'creatore' => Auth::user()->name,
                        'creato' => date('Y-m-d'),
                    ]);
                    $categorieEsistenti[] = $categoria;
                    $cat_id = $cat->id;
                } else {
                    $cat_id = Categorie::where('nome', $categoria)->first()->id;
                }

                Spese::create([
                    'nome' => $categoria,
                    'importo' => $row[$i],
                    'categorie_id' => $cat_id,
                    'data' => $anno . '-' . $mese . '-01',
                    'attivo' => 1,
                    'creatore' => Auth::user()->name,
                    'creato' => date('Y-m-d'),
                ]);
            }
        }
    }

    return redirect()->route('spese/importa')->with('success', 'File Excel elaborato con successo');
}

*/
