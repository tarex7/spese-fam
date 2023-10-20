<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrate;
use App\Models\Categorie;
use App\Models\CategorieEntrate;
use App\Models\Tipologia;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
class EntrateController extends Controller
{
    public function index()
    {
        /*  breadcrumbs(function (BreadcrumbsGenerator $trail) use ($category, $product) {
              $trail->parent('entrate', $category);
              $trail->push($product->name, route('product.show', $product));
          });*/

        $entrate = Entrate::select('entrate.*')
            ->where('entrate.attivo', 1)
            ->whereMonth('data', '=', date('n'))
            ->whereYear('data', '=', date('Y'))
            ->leftJoin('categorie', 'entrate.categorieentrate_id', 'categorie.id')
            ->leftJoin('tipologia', 'entrate.tipologia_id', 'tipologia.id')
            ->select('entrate.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->paginate(10);

        $totale = $entrate->sum('importo'); //dd($entrate)
        ;


        $cat = CategorieEntrate::where('attivo', 1)->orderBy('nome', 'ASC')->get();
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

        // dd($entrate);
        return view('entrate.entrate')
            ->with('entrate', $entrate)
            ->with('mesi', $mesi)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('years', $years)
            ->with('cat', $cat_opt)
            ->with('entrate_id', null)
            ->with('totale', $totale)
            ->with('tip', $tip_opt);
    }


    public function aggiungi(Request $request)
    {

        //dd($request->all());


        $create = Entrate::create([

            'nome' => $request->nome_add,
            'data' => date('Y-m-d', strtotime($request->data_add)),
            'importo' => $request->importo_add,
            'categorie_id' => $request->categorie_add,
            'tipologia_id' => $request->tipologia_add,
            'attivo' => 1,
            'creatore' => Auth::user()->name,
            'creato' => date('Y-m-d'),
        ]);

        $entrate = Entrate::select('entrate.*')
            ->where('entrate.attivo', 1)
            ->leftJoin('categorie', 'entrate.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'entrate.tipologia_id', 'tipologia.id')
            ->select('entrate.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia')
            ->get();

        $totale = $entrate->sum('importo');


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
        return view('entrate.entrate')
            ->with('success', 'Spesa aggiunta!')
            ->with('cat', $cat_opt)
            ->with('entrate', $entrate)
            ->with('years', $years)
            ->with('mesi', $mesi)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('entrate_id', $create->id)
            ->with('totale', $totale)
            ->with('tip', $tip_opt);
    }


    public function salva(Request $request)
    {

        //dd($request->all());
        foreach ($request->entrate as $k => $s) {

            Entrate::where('id', $k)
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
        return redirect()->route('entrate')->with('success', 'Modifica salvata!');
    }


    public function elimina($id)
    {

        Entrate::where('id', $id)->update([
            'attivo' => 0,

        ]);

        return redirect()->route('entrate')
            ->with('success', 'Spesa eliminata! 😁👍');
    }


    public function filtra(Request $request)
    {

        //  dd($request->all());
        $mese = $request->mese;
        $anno = $request->anno;
        $mese_sel = 0;
        $entrate = Entrate::select('entrate.*')
            ->where('entrate.attivo', 1)
            ->leftJoin('categorie', 'entrate.categorie_id', 'categorie.id')
            ->leftJoin('tipologia', 'entrate.tipologia_id', 'tipologia.id')
            ->select('entrate.*', 'categorie.nome as categoria', 'tipologia.nome as tipologia');


        if ($mese != '0') {

            $entrate->whereMonth('data', '=', $mese);
            $mese_sel = $mese;
        }


        if ($anno != '0') {

            $entrate->whereYear('data', '=', $anno);

            $anno_sel = $anno;
            // dd($anno_sel);
        }




        //dd($entrate);

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

        //dd($entrate->get());
        // dd($request->query());

        $ris = $entrate->where('importo', '!=', 0)->where('importo','!=',null)->paginate(10)->appends($request->query());
        $totale = $ris->sum('importo');




        // dd($ris);
        return view('entrate.entrate')
            ->with('entrate', $ris)
            ->with('mesi', $mesi)
            ->with('years', $years)
            ->with('anno_sel', $anno_sel)
            ->with('mese_sel', $mese_sel)
            ->with('cat', $cat_opt)
            ->with('totale', $totale)
            ->with('entrate_id', null)
            ->with('tip', $tip_opt);
    }


    public function elenco(Request $request)
    {
        //dd($request->all());
        $year = date('Y');

        if ($request->anno != null) {

            $year = $request->anno;
        }
        $entrateMensili = array();
        // dd($request->all());
        //dd($year);
        for ($i = 1; $i <= 12; $i++) {
            //
            $spe = array();
            $sp = Entrate::where('attivo', 1)->whereMonth('data', $i)->whereYear('data', $year)->select('importo')->get();

            foreach ($sp as $s) {
                array_push($spe, $s->importo);
            }

            $entrateMensili[$i] = array_sum($spe);
        }
        // dd($entrateMensili);

        $entratePerCategoria = Entrate::join('categorie', 'entrate.categorie_id', '=', 'categorie.id')
            ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
            ->groupBy('categorie.nome', 'mese')
            ->whereYear('data', date('Y'))
            ->get();

        if ($year != null) {
            $entratePerCategoria = Entrate::join('categorie', 'entrate.categorie_id', '=', 'categorie.id')
                ->select('categorie.nome as categoria', DB::raw('MONTH(data) as mese'), DB::raw('SUM(importo) as importo'))
                ->groupBy('categorie.nome', 'mese')
                ->whereYear('data', $year)
                ->get();
            // dd($entratePerCategoria);

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

        //dd($entratePerCategoria);
        $entrateRaggruppate = [];

        foreach ($entratePerCategoria as $spesa) {

            $categoria = $spesa->categoria;
            $mese = $spesa->mese;
            $importo = $spesa->importo;


            if (!isset($entrateRaggruppate[$categoria])) {
                $entrateRaggruppate[$categoria] = [];
            }
            if ($mese != null) {

                $entrateRaggruppate[$categoria][$mese] = $importo;
            }
        }

        // dd($entrateRaggruppate, $entrateMensili,$entratePerCategoria);
        if (count($entratePerCategoria) == 0) {
            $entrateRaggruppate = array(
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

        return view('entrate.elenco')
            ->with('years', $years)
            ->with('anno_sel', $anno_sel)
            ->with('entrateRaggruppate', $entrateRaggruppate)
            ->with('entrate_mensili', $entrateMensili);
    }




    public function importa()
    {
        return view('entrate/importa');
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

                    Entrate::create([
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

        return redirect()->route('entrate/importa')->with('success', 'File Excel elaborato con successo');
    }
}
