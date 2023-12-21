<?php

namespace App\Http\Controllers;

use App\Models\entrate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\AddSpesaRequest;
use App\Http\Requests\EditEntrateRequest;
use App\Models\CategorieEntrate;

class entrateController extends Controller
{

    private function EntrateQuery()
    {
        return Entrate::with(['categorie_entrate', 'tipologia'])
            ->where('attivo', 1)
            ->where('importo', '>', 0);
    }


    public function index($anno = null)
    {
        
        $now = Carbon::now();
        $anno_sel = $anno ?? $now->year;
        $mese_sel = $now->month;

        $entrate = $this->EntrateQuery()
        ->with('categorie_entrate', 'tipologia')
        ->get();

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
        return redirect()->route('entrate')->with('success', 'Entrata Aggiunta 👍');
    }


    public function salva(EditEntrateRequest $request)
    {
       // dd($request->all());
        DB::transaction(function () use ($request) {
            foreach ($request->entrate as $id => $data) {
                $entrata = Entrate::find($id);

                if ($entrata) {
                    $entrata->update([
                        'data' => $data['data'],
                        'importo' => $data['importo'],
                        'categorieentrate_id' => $data['categorieentrate'],
                        'tipologia_id' => $data['tipologia'],
                        'modificato' => now(),
                        'modificatore' => Auth::user()->name,
                    ]);
                }
            };
        });

      
        return redirect()->route('entrate', ['anno' => $request->anno_sel])
            ->with('success', 'Modifica salvata!');
    }


    public function elimina($id)
    {

        entrate::where('id', $id)->update([
            'attivo' => 0,

        ]);

        return redirect()->route('entrate')
            ->with('success', 'Entrata eliminata! 😁👍');
    }




    public function filtra(Request $request)
    {
        $mese = $request->mese ?? 0;
        $anno = $request->anno ?? 0;
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        $entrate = $this->entrateQuery()
            ->when($mese != 0, function ($query) use ($mese) {
                return $query->whereMonth('data', '=', $mese);
            })
            ->when($anno != 0, function ($query) use ($anno) {
                return $query->whereYear('data', '=', $anno);
            });

        return $entrate->paginate($limit, ['*'], 'page', $page);
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



    public function elenco($year)
    {
        //dd($request->all());

        // Ottiene l'anno dalla request o usa l'anno corrente
        $year = $year ?? date('Y');

        $years = range(date('Y') - 10, date('Y') + 10);
        $years = [0 => 'Seleziona'] + array_combine($years, $years);

        return view('entrate.elenco', [
            'years' => $years,
            'anno' => $year,
        ]);
    }

    public function getElenco($year)
    {

        $entratePerCategoria = Entrate::join('categorie_entrate', 'entrate.categorieentrate_id', '=', 'categorie_entrate.id')
            ->select('categorie_entrate.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie_entrate.nome', 'mese')
            ->whereYear('data', $year)
            ->get();

        $entrateRaggruppate = $entratePerCategoria->groupBy('categoria')
            ->mapWithKeys(function ($item, $key) {
                $total = $item->sum('importo');
                return [$key => ['importi_mensili' => $item->pluck('importo', 'mese')->all() + array_fill(1, 12, 0), 'total' => $total]];
            });

        return $entrateRaggruppate;
    }




    public function importa()
    {
        return view('entrate/importa');
    }


    public function carica_file(Request $request)
    {
       // dd($request->all());

        $anno = $request->anno;
        $request->validate([
            'excel_file' => 'required|mimes:xls,xlsx',
            'anno' => 'required'
        ],
        [
            'excel_file.required' => 'Il file Excel è obbligatorio.',
            'excel_file.mimes' => 'Il file deve essere un documento di tipo Excel (xls o xlsx).',
            'anno.required' => 'L\'anno è un campo obbligatorio.'
        ]);

        $file = $request->file('excel_file');
        $data = Excel::toCollection([], $file);

        $categorieentrateEsistenti = Categorieentrate::where('attivo', 1)->pluck('nome')->all();
        $mesi = array_flip(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']);

        foreach ($data as $sheet) {
            foreach ($sheet as $row) {
                for ($i = 1; $i < count($row); $i++) {
                    $row[$i] = $row[$i] ?? 0.00;
                    $mese = $mesi[$i] ?? '01';
                    $categoria = strtolower($row[0]);

                    if (!in_array($categoria, $categorieentrateEsistenti)) {
                        $cat = Categorieentrate::create([
                            'nome' => $categoria,
                            'attivo' => 1,
                            'creatore' => Auth::user()->name,
                            'creato' => date('Y-m-d'),
                        ]);
                        $categorieentrateEsistenti[] = $categoria;
                        $cat_id = $cat->id;
                    } else {
                        $cat_id = Categorieentrate::where('nome', $categoria)->first()->id;
                    }

                    entrate::create([
                        'nome' => $categoria,
                        'importo' => $row[$i],
                        'categorieentrate_id' => $cat_id,
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
