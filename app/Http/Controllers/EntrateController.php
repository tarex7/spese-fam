<?php

namespace App\Http\Controllers;

use App\Models\entrate;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\AddSpesaRequest;
use App\Http\Requests\EditSpesaRequest;

class entrateController extends Controller
{

    private function entrateQuery()
    {
        return entrate::with(['categorie_entrate', 'tipologia'])
            ->where('attivo', 1);
    }

   

    

    public function index()
    {
        $now = Carbon::now();

        $anno_sel = $now->year;
        $mese_sel = $now->month;

        $entrate = $this->entrateQuery()
            ->whereMonth('data', '=', date('n'))
            ->whereYear('data', '=', date('Y'))
            ->paginate(10);


        $totale = $entrate->sum('importo');

        return view('entrate.entrate')
            ->with('entrate', $entrate)
            ->with('anno', $anno_sel)
            ->with('mese', $mese_sel)
            ->with('years', Entrate::getYearsOptions())
            ->with('mesi', Entrate::getMesiOptions())
            ->with('cat', entrate::getCategorieOptions())
            ->with('tip', entrate::getTipologiaOptions())
            ->with('entrate_id', null)
            ->with('totale', $totale);
    }


    public function aggiungi(AddSpesaRequest $request)
    {
       // dd($request->all());
        entrate::creaDaRichiesta($request);
        return redirect()->route('entrate')->with('success', 'Spesa Aggiunta 👍');
    }


    public function salva(EditSpesaRequest $request)
    {

        // dd($request->all());
        foreach ($request->entrate as $k => $s) {

            entrate::where('id', $k)
                ->update([
                    'data' => $s['data'],
                    'importo' => $s['importo'],
                    'categorie_id' => $s['categorie'],
                    'tipologia_id' => $s['tipologia'],
                    'modificato' => now(),
                    'modificatore' => Auth::user()->name,
                ]);
        }
        return redirect()->route('entrate')->with('success', 'Modifica salvata!');
    }


    public function elimina($id)
    {

        entrate::where('id', $id)->update([
            'attivo' => 0,

        ]);

        return redirect()->route('entrate')
            ->with('success', 'Spesa eliminata! 😁👍');
    }




    public function filtra(Request $request)
    {
        //  dd($request->all());
        $mese = $request->mese ?? 0;
        $anno = $request->anno ?? 0;

        $entrate = $this->entrateQuery()
            ->when($mese != 0, function ($query) use ($mese) {
                return $query->whereMonth('data', '=', $mese);
            })
            ->when($anno != 0, function ($query) use ($anno) {
                return $query->whereYear('data', '=', $anno);
            });


        $totale = $entrate->sum('importo');

        $ris = $entrate->paginate(10)->appends($request->query());

        return view('entrate.entrate')
            ->with('entrate', $ris)
            ->with('mesi', $this->getMesiOptions())
            ->with('years', $this->getYearsOptions())
            ->with('cat', entrate::getCategorieOptions())
            ->with('tip', entrate::getTipologiaOptions())
            ->with('anno', $anno)
            ->with('mese', $mese)
            ->with('totale', $totale)
            ->with('entrate_id', null);
    }

    public function calcolaentrateMensili($year)
    {
        return entrate::where('attivo', 1)
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

        $entrateMensili = entrate::where('attivo', 1)
            ->whereYear('data', $year)
            ->groupBy(DB::raw('MONTH(data)'))
            ->select(DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as totale'))
            ->pluck('totale', 'mese');


        $entratePerCategoria = entrate::join('categorie', 'entrate.categorie_id', '=', 'categorie.id')
            ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie.nome', 'mese')
            ->whereYear('data', $year)
            ->get();

        $years = range(date('Y') - 10, date('Y') + 10);
        $years = [0 => 'Seleziona'] + array_combine($years, $years);

        $entrateRaggruppate = $entratePerCategoria->groupBy('categoria')
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item->pluck('importo', 'mese')->all() + array_fill(1, 12, 0)];
            });

        return view('entrate.elenco', [
            'years' => $years,
            'anno_sel' => $year,
            'entrateRaggruppate' => $entrateRaggruppate,
            'entrate_mensili' => $entrateMensili->toArray()
        ]);
    }




    public function importa()
    {
        return view('entrate/importa');
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

                entrate::create([
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

    return redirect()->route('entrate/importa')->with('success', 'File Excel elaborato con successo');
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

                entrate::create([
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

    return redirect()->route('entrate/importa')->with('success', 'File Excel elaborato con successo');
}

*/

