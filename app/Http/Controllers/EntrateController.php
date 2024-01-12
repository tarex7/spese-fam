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
    // Questa funzione restituisce una query base per le entrate.
    // Include le relazioni 'categorie_entrate' e 'tipologia'.
    // Filtra per le entrate attive e con un importo maggiore di zero.
    return Entrate::with(['categorie_entrate', 'tipologia'])
        ->where('attivo', 1)
        ->where('importo', '>', 0);
}

public function index($anno = null)
{
    // Utilizza Carbon per ottenere la data e l'ora attuali.
    $now = Carbon::now();

    // Imposta l'anno selezionato all'anno fornito, o usa l'anno corrente se non specificato.
    $anno_sel = $anno ?? $now->year;

    // Imposta il mese selezionato al mese corrente.
    $mese_sel = $now->month;

    // Esegue la query predefinita delle entrate (definita in EntrateQuery).
    // Include le relazioni 'categorie_entrate' e 'tipologia'.
    $entrate = $this->EntrateQuery()->get();

    // Calcola il totale delle entrate sommando l'importo di ogni entrata.
    $totale = $entrate->sum('importo');

    
    return view('entrate.entrate')
        ->with('entrate', $entrate) // passa le entrate alla vista
        ->with('anno', $anno_sel) // passa l'anno selezionato
        ->with('mese', $mese_sel) // passa il mese selezionato
        ->with('years', Entrate::getYearsOptions()) // passa le opzioni degli anni disponibili
        ->with('mesi', Entrate::getMesiOptions()) // passa le opzioni dei mesi disponibili
        ->with('cat', Entrate::getCategorieOptions()) // passa le opzioni delle categorie di entrate
        ->with('tip', Entrate::getTipologiaOptions()) // passa le opzioni delle tipologie di entrate
        ->with('entrate_id', null) // passa un identificativo per le entrate (null in questo caso)
        ->with('totale', $totale); // passa il totale delle entrate
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
        $entrateMensili = Entrate::where('attivo', 1)
            ->whereYear('data', $year)
            ->get()
            ->groupBy(function ($data) {
                return Carbon::parse($data->data)->format('m'); // raggruppa per mese
            })
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item->sum('importo')]; // calcola la somma per ogni mese
            })
            ->toArray(); // Converte la collection in un array

        $formatted = array_map(function ($item) {
            return number_format($item, 2, '.', '');
        }, $entrateMensili);

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
