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
        return Spese::where('attivo', 1)->where('importo', '>', 0);
    }



    public function index()
    {
        $now = Carbon::now();

        $anno_sel = $now->year;
        $mese_sel = $now->month;

        $spese = $this->SpeseQuery()
            // ->whereMonth('data', '=', date('n'))
            // ->whereYear('data', '=', date('Y'))
            
            ->get();

        $totale = $spese->sum('importo');

        return view('spese.spese')
            ->with('spese', $spese)
            ->with('anno', $anno_sel)
            ->with('mese', $mese_sel)
            ->with('years', Spese::getYearsOptions())
            ->with('mesi', Spese::getMesiOptions())
            ->with('cat', Spese::getCategorieOptions())
            ->with('tip', Spese::getTipologiaOptions())
            ->with('spese_id', null)
            ->with('totale', $totale);
    }


    public function aggiungi(AddSpesaRequest $request)
    {
        Spese::creaDaRichiesta($request);
        return redirect()->route('spese')->with('success', 'Spesa Aggiunta 👍');
    }


    public function salva(EditSpesaRequest $request)
    {

        // dd($request->all());
        foreach ($request->spese as $k => $s) {

            Spese::where('id', $k)
                ->update([
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
        // dd($request->all());
        $mese = $request->mese ?? 0;
        $anno = $request->anno ?? 0;
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;
    
        $spese = $this->SpeseQuery()
            ->when($mese != 0, function ($query) use ($mese) {
                return $query->whereMonth('data', '=', $mese);
            })
            ->when($anno != 0, function ($query) use ($anno) {
                return $query->whereYear('data', '=', $anno);
            });
    
        return $spese->paginate($limit, ['*'], 'page', $page);
    }


    
    public function calcolaSpeseMensili($year)
    {
        return Spese::where('attivo', 1)
            ->whereYear('data', $year)
            ->get()
            ->groupBy(function ($data) {
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
}
