<?php

namespace App\Http\Controllers;

use App\Models\Spese;
use Illuminate\Http\Request;
use App\Models\CategorieSpese;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\AddSpesaRequest;
use App\Http\Requests\EditSpesaRequest;

class SpeseController extends Controller
{

    private function SpeseQuery()
    {
        return Spese::where('attivo', 1)->where('importo', '>', 0);
    }


    public function index($anno = null)
    {
        $now = Carbon::now();
        $anno_sel = $anno ?? $now->year;
        $mese_sel = $now->month;

        $spese = $this->SpeseQuery()
            ->with('categoria', 'tipologia')
            ->get();

        $totale = $spese->sum('importo');

        return view('spese.spese')
            ->with('spese', $spese)
            ->with('anno', $anno_sel)
            ->with('mese', $mese_sel)
            ->with('years', Spese::getYearsOptions())
            ->with('mesi', Spese::getMesiOptions())
            ->with('cat', Spese::getCategorieSpeseOptions())
            ->with('tip', Spese::getTipologiaOptions())
            ->with('spese_id', null)
            ->with('totale', $totale);
    }


    public function aggiungi(AddSpesaRequest  $request)
    {
        // dd($request->all());
        Spese::creaDaRichiesta($request);
        return redirect()->route('spese')->with('success', 'Spesa Aggiunta 👍');
    }


    public function salva(EditSpesaRequest $request)
    {
        // dd($request->all());
        DB::transaction(function () use ($request) {
            foreach ($request->spese as $id => $data) {
                $spesa = Spese::find($id);

                if ($spesa) {
                    $spesa->update([
                        'data' => $data['data'],
                        'importo' => $data['importo'],
                        'categoriespese_id' => $data['categoriespese'],
                        'tipologia_id' => $data['tipologia'],
                        'modificato' => now(),
                        'modificatore' => Auth::user()->name,
                    ]);
                }
            };
        });


        return redirect()->route('spese', ['anno' => $request->anno_sel])
            ->with('success', 'Modifica salvata!');
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



    public function calcolaSpeseMensili($year) {
        $speseMensili = Spese::where('attivo', 1)
            ->whereYear('data', $year)
            ->get()
            ->groupBy(function ($data) {
                return Carbon::parse($data->data)->format('m'); // raggruppa per mese
            })
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item->sum('importo')]; // calcola la somma per ogni mese
            })
            ->toArray(); // Converte la collection in un array
    
        $formatted = array_map(function($item) {
            return number_format($item, 2, '.', '');
        }, $speseMensili);
    
        // Inizializza un array con 12 elementi (tutti zero)
        $result = array_fill(1, 12, 0);
    
        // Sostituisce i valori calcolati nei mesi corrispondenti
        foreach ($formatted as $month => $value) {
            $result[intval($month)] = $value;
        }
    
        // Assicurati che il risultato sia sempre un array numerico con 12 elementi
        return array_values($result);
    }
    





    public function elenco($year)
    {
        //dd($request->all());

        // Ottiene l'anno dalla request o usa l'anno corrente
        $year = $year ?? date('Y');

        $years = range(date('Y') - 10, date('Y') + 10);
        $years = [0 => 'Seleziona'] + array_combine($years, $years);

        return view('spese.elenco', [
            'years' => $years,
            'anno' => $year,
        ]);
    }

    public function getElenco($year)
    {

        $spesePerCategoria = Spese::join('categorie_spese', 'spese.categoriespese_id', '=', 'categorie_spese.id')
            ->select('categorie_spese.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie_spese.nome', 'mese')
            ->whereYear('data', $year)
            ->get();

        $speseRaggruppate = $spesePerCategoria->groupBy('categoria')
            ->mapWithKeys(function ($item, $key) {
                $total = $item->sum('importo');
                return [$key => ['importi_mensili' => $item->pluck('importo', 'mese')->all() + array_fill(1, 12, 0), 'total' => $total]];
            });

        return $speseRaggruppate;
    }




    public function importa()
    {
        return view('spese/importa');
    }


    public function carica_file(Request $request)
    {
        // dd($request->all());

        $anno = $request->anno;
        $request->validate(
            [
                'excel_file' => 'required|mimes:xls,xlsx',
                'anno' => 'required'
            ],
            [
                'excel_file.required' => 'Il file Excel è obbligatorio.',
                'excel_file.mimes' => 'Il file deve essere un documento di tipo Excel (xls o xlsx).',
                'anno.required' => 'L\'anno è un campo obbligatorio.'
            ]
        );

        $file = $request->file('excel_file');
        $data = Excel::toCollection([], $file);

        $categoriespeseEsistenti = CategorieSpese::where('attivo', 1)->pluck('nome')->all();
        $mesi = array_flip(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']);

        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                for ($i = 1; $i < count($row); $i++) {
                    $row[$i] = $row[$i] ?? 0.00;
                    $mese = $mesi[$i] ?? '01';
                    $categoria = strtolower($row[0]);

                    if (!in_array($categoria, $categoriespeseEsistenti)) {
                        $cat = CategorieSpese::create([
                            'nome' => $categoria,
                            'attivo' => 1,
                            'creatore' => Auth::user()->name,
                            'creato' => date('Y-m-d'),
                        ]);
                        $categoriespeseEsistenti[] = $categoria;
                        $cat_id = $cat->id;
                    } else {
                        $cat_id = CategorieSpese::where('nome', $categoria)->first()->id;
                    }

                    Spese::create([
                        'nome' => $categoria,
                        'importo' => $row[$i],
                        'categoriespese_id' => $cat_id,
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
