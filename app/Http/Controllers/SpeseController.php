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
        // Ottiene l'anno corrente usando Carbon
        $now = Carbon::now();

        // Imposta l'anno selezionato al valore fornito o all'anno corrente se non fornito
        $anno_sel = $anno ?? $now->year;

        // Imposta il mese selezionato al mese corrente
        $mese_sel = $now->month;

        // Esegue una query predefinita per ottenere le spese (definita in SpeseQuery)
        // Include le relazioni 'categoria' e 'tipologia' nella query
        $spese = $this->SpeseQuery()
            ->with('categoria', 'tipologia')
            ->get();

        // Calcola il totale delle spese sommando l'importo di ogni spesa
        $totale = $spese->sum('importo');

        // Restituisce la vista 'spese.spese', passando i dati delle spese e le informazioni aggiuntive
        return view('spese.spese')
            ->with('spese', $spese) // passa le spese alla vista
            ->with('anno', $anno_sel) // passa l'anno selezionato
            ->with('mese', $mese_sel) // passa il mese selezionato
            ->with('years', Spese::getYearsOptions()) // passa le opzioni degli anni disponibili
            ->with('mesi', Spese::getMesiOptions()) // passa le opzioni dei mesi disponibili
            ->with('cat', Spese::getCategorieSpeseOptions()) // passa le opzioni delle categorie di spese
            ->with('tip', Spese::getTipologiaOptions()) // passa le opzioni delle tipologie di spese
            ->with('spese_id', null) // passa un identificativo per le spese (null in questo caso)
            ->with('totale', $totale); // passa il totale delle spese
    }



    public function aggiungi(AddSpesaRequest $request)
    {

        // dd($request->all());

        // Chiama il metodo statico 'creaDaRichiesta' sul modello Spese. 
        // Questo metodo è responsabile della creazione di una nuova spesa nel database.
        // La logica esatta di come una spesa viene creata da una richiesta è definita in questo metodo.
        Spese::creaDaRichiesta($request);

        return redirect()->route('spese')->with('success', 'Spesa Aggiunta 👍');
    }



    public function salva(EditSpesaRequest $request)
    {

        // dd($request->all());

        // Avvia una transazione di database. Questo garantisce che tutte le operazioni all'interno 
        // della transazione siano eseguite con successo prima di confermarle (commit). 
        // Se un'operazione fallisce, tutte le modifiche vengono annullate (rollback).

        DB::transaction(function () use ($request) {
            // Itera su ogni spesa nella richiesta
            foreach ($request->spese as $id => $data) {
                // Trova la spesa corrispondente nel database usando il suo ID
                $spesa = Spese::find($id);

                // Se la spesa esiste, la aggiorna con i nuovi dati
                if ($spesa) {
                    $spesa->update([
                        'data' => $data['data'], // Aggiorna la data della spesa
                        'importo' => $data['importo'], // Aggiorna l'importo della spesa
                        'categoriespese_id' => $data['categoriespese'], // Aggiorna l'ID della categoria di spesa
                        'tipologia_id' => $data['tipologia'], // Aggiorna l'ID della tipologia di spesa
                        'modificato' => now(), // Imposta il timestamp della modifica
                        'modificatore' => Auth::user()->name, // Imposta l'utente che ha effettuato la modifica
                    ]);
                }
            }
        });

        return redirect()->route('spese', ['anno' => $request->anno_sel])
            ->with('success', 'Modifica salvata!');
    }




    public function elimina($id)
    {
        // Aggiorna il record della spesa con l'ID specificato.

        Spese::where('id', $id)->update([
            'attivo' => 0,
        ]);


        return redirect()->route('spese')
            ->with('success', 'Spesa eliminata! 😁👍');
    }





    public function filtra(Request $request)
    {

        // dd($request->all());

        // Ottiene i valori di mese e anno dalla richiesta, con valori predefiniti se non sono presenti.
        $mese = $request->mese ?? 0;
        $anno = $request->anno ?? 0;

        // Ottiene i parametri di paginazione dalla richiesta, con valori predefiniti.
        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        // Avvia la query predefinita delle spese (definita in SpeseQuery).
        $spese = $this->SpeseQuery()
            // Applica il filtro del mese se è stato fornito un mese diverso da 0.
            ->when($mese != 0, function ($spese) use ($mese) {
                $spese->whereMonth('data', '=', $mese);
            })
            // Applica il filtro dell'anno se è stato fornito un anno diverso da 0.
            ->when($anno != 0, function ($spese) use ($anno) {
                 $spese->whereYear('data', '=', $anno);
            });

            //dd($spese->toSql(), $spese->getBindings());

        return $spese->paginate($limit, ['*'], 'page', $page);
    }




    public function calcolaSpeseMensili($year)
    {
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

        $formatted = array_map(function ($item) {
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
        // Esegue una query per ottenere le spese, unendole con la tabella 'categorie_spese'
        // basata sull'ID della categoria di spesa. Seleziona il nome della categoria, il mese
        // e la somma dell'importo delle spese, raggruppate per nome della categoria e mese.
        $spesePerCategoria = Spese::join('categorie_spese', 'spese.categoriespese_id', '=', 'categorie_spese.id')
            ->select('categorie_spese.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie_spese.nome', 'mese')
            ->whereYear('data', $year) // Filtra le spese per l'anno specificato
            ->get();

        // Raggruppa le spese per categoria.
        $speseRaggruppate = $spesePerCategoria->groupBy('categoria')
            ->mapWithKeys(function ($item, $key) {
                // Calcola il totale per ciascuna categoria
                $total = $item->sum('importo');
                // Crea un array che contiene gli importi mensili per ogni categoria.
                // Utilizza 'pluck' per estrarre l'importo di ciascun mese e 'array_fill' 
                // per assicurarsi che ci siano importi per tutti i 12 mesi.
                return [$key => [
                    'importi_mensili' => $item->pluck('importo', 'mese')->all() + array_fill(1, 12, 0),
                    'total' => $total
                ]];
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

        // L'anno viene estratto dalla richiesta
        $anno = $request->anno;

        // Validazione della richiesta: assicura che il file Excel e l'anno siano forniti e che il file sia nel formato corretto
        $request->validate(
            [
                'excel_file' => 'required|mimes:xls,xlsx', // Il file deve essere Excel
                'anno' => 'required' // L'anno è obbligatorio
            ],
            [
                // Messaggi di errore personalizzati per la validazione
                'excel_file.required' => 'Il file Excel è obbligatorio.',
                'excel_file.mimes' => 'Il file deve essere un documento di tipo Excel (xls o xlsx).',
                'anno.required' => 'L\'anno è un campo obbligatorio.'
            ]
        );

        // Recupera il file Excel dalla richiesta
        $file = $request->file('excel_file');

        // Converte il file Excel in una collezione
        $data = Excel::toCollection([], $file);

        // Recupera tutte le categorie di spese esistenti che sono attive
        $categoriespeseEsistenti = CategorieSpese::where('attivo', 1)->pluck('nome')->all();

        // Mappa dei mesi per convertire i numeri dei mesi in formati di due cifre
        $mesi = array_flip(['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12']);

        // Elabora ogni foglio del file Excel
        foreach ($data as $sheet) {
            // Elabora ogni riga del foglio
            foreach ($sheet as $row) {
                // Elabora ogni cella nella riga, iniziando dalla seconda (indice 1)
                for ($i = 1; $i < count($row); $i++) {
                    // Imposta un valore predefinito per la cella se è null
                    $row[$i] = $row[$i] ?? 0.00;

                    // Determina il mese corrispondente alla cella
                  // $mese = $mesi[$i] ?? '01';
                   $mese = isset($mesi[sprintf("%02d", $i + 1)]) ? $mesi[sprintf("%02d", $i + 1)] : '01';

                    // La categoria è la prima cella della riga, convertita in minuscolo
                    $categoria = strtolower($row[0]);

                    // Controlla se la categoria esiste, se non esiste, la crea
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
                        // Se la categoria esiste già, ne recupera l'ID
                        $cat_id = CategorieSpese::where('nome', $categoria)->first()->id;
                    }

                    // Crea un nuovo record di spesa con i dati estratti
                    if( $row[$i] > 0) {
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
        }

        // Dopo aver elaborato il file, reindirizza l'utente alla pagina di importazione con un messaggio di successo
        return redirect()->route('spese/importa')->with('success', 'File Excel elaborato con successo');
    }
}
