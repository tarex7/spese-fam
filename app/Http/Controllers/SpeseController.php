<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use App\Models\Categorie;
use App\Models\Tipologia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;

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
        $anno_sel = date('Y');
        $mese_sel = date('n');

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


    public function aggiungi(Request $request)
    {
       //dd($request->all());

        $request->validate([

            'categorie_add' => 'required|exists:categorie,id',
            'data_add' => 'required|date',
            'importo_add' => 'required|numeric',
            'tipologia_add' => 'required|exists:tipologia,id',
        ],[
            'categorie_add.required' => 'Scegli una categoria',
            'categorie_add.exists' => 'La categoria selezionata non è valida',
            'data_add.required' => 'Inserisci la data',
            'importo_add.required' => 'Inserisci un importo',
            'tipologia_add.required' => 'Scegli una tipologia',
            'tipologia_add.exists' => 'La tipologia selezionata non è valida',
        ]);


        $create = Spese::create([

            'nome' => $request->nome_add,
            'data' => date('Y-m-d', strtotime($request->data_add)),
            'importo' => $request->importo_add,
            'categorie_id' => $request->categorie_add,
            'tipologia_id' => $request->tipologia_add,
            'attivo' => 1,
            'creatore' => Auth::user()->name,
            'creato' => date('Y-m-d'),
        ]);


        $spese = $this->SpeseQuery()
            ->paginate(10);

        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);

        $years = [0 => 'Seleziona'];
        foreach ($anni as $key => $a) {
            $years[$a] = $a;
        }

        return redirect()->route('spese');
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
        $mese = $request->mese;
        $anno = $request->anno;
        $mese_sel = 0;

        $spese = $this->SpeseQuery();
        if ($mese != '0') {
            $spese->whereMonth('data', '=', $mese);
            $mese_sel = $mese;
        }

        if ($anno != '0') {
            $spese->whereYear('data', '=', $anno);
            $anno_sel = $anno;
        }

        $totale = $spese->sum('importo'); // Calcola il totale prima della paginazione

        $ris = $spese->paginate(10)->appends($request->query());

        return view('spese.spese')
            ->with('spese', $ris)
            ->with('mesi' , $this->getMesiOptions())
            ->with('years', $this->getYearsOptions())
            ->with('cat', Spese::getCategorieOptions())
            ->with('tip', Spese::getTipologiaOptions())
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
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

        if ($request->anno != null) {

            $year = $request->anno;
        }
       /* $speseMensili = array();*/

       // $speseMensili = $this->calcolaSpeseMensili($year);
        for ($i = 1; $i <= 12; $i++) {
            //
            $spe = array();
            $sp = Spese::where('attivo', 1)->whereMonth('data', $i)->whereYear('data', $year)->select('importo')->get();

            foreach ($sp as $s) {
                array_push($spe, $s->importo);
            }

            $speseMensili[$i] = array_sum($spe);
        }
        // dd($speseMensili);





        $spesePerCategoria = Spese::join('categorie', 'spese.categorie_id', '=', 'categorie.id')
            ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie.nome', 'mese')
            ->whereYear('data', date('Y'))
            ->get();

        foreach ($spesePerCategoria as $sp) {

        }

        if ($year != null) {
            $spesePerCategoria = Spese::join('categorie', 'spese.categorie_id', '=', 'categorie.id')
                ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
                ->groupBy('categorie.nome', 'mese')
                ->whereYear('data', $year)
                ->get();
        }


        $anni = range(date('Y') - 10, date('Y') + 10);
        $anni = array_combine($anni, $anni);
        $years = [0 => 'Seleziona'];

        $anno_sel = date('Y');
        if ($year != null) {
            $anno_sel = $year;
        }

        foreach ($anni as $key => $a) {
            $years[$a] = $a;
        }

        //dd($spesePerCategoria);
        $speseRaggruppate = [];

        foreach ($spesePerCategoria as $spesa) {

            $categoria = $spesa->categoria;
            $mese = $spesa->mese;
            $importo = $spesa->importo;


            if (!isset($speseRaggruppate[$categoria])) {
                $speseRaggruppate[$categoria] = [];
            }
            if ($mese != null) {

                $speseRaggruppate[$categoria][$mese] = $importo;

               for($i = 1;$i <= 12;$i++) {

                   if(!isset($speseRaggruppate[$categoria][$i])) {

                       $speseRaggruppate[$categoria][$i] = 0;
                   }
               }
            }
        }

        if (count($spesePerCategoria) == 0) {
            $speseRaggruppate = array(
                '' => array(
                    1 => 0,
                    2 => 0,
                    3 => 0,
                    4 => 0,
                    5 => 0,
                    6 => 0,
                    7 => 0,
                    8 => 0,
                    9 => 0,
                    10 => 0,
                    11 => 0,
                    12 => 0,
                )

            );
        }

        return view('spese.elenco')
            ->with('years', $years)
            ->with('anno_sel', $anno_sel)
            ->with('speseRaggruppate', $speseRaggruppate)
            ->with('spese_mensili', $speseMensili);
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
