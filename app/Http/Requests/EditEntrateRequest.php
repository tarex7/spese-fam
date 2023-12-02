<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditEntrateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'entrate.*.categorieentrate' => 'required|exists:categorie_entrate,id',
            'entrate.*.data' => 'required|date',
            'entrate.*.importo' => 'required|numeric',
            'entrate.*.tipologia' => 'required|exists:tipologia,id',
        ];
    }

    public function messages()
    {
        return [
            'entrate.*.categorie.required' => 'Scegli una categoria per tutte le entrate',
            'entrate.*.categorie.exists' => 'La categoria selezionata non è valida',
            'entrate.*.data.required' => 'Inserisci la data per tutte le entrate',
            'entrate.*.data.date' => 'La data deve essere una data valida',
            'entrate.*.importo.required' => 'Inserisci l\'importo per tutte le entrate',
            'entrate.*.importo.numeric' => 'L\'importo deve essere un valore numerico',
            'entrate.*.tipologia.required' => 'Scegli una tipologia per tutte le entrate',
            'entrate.*.tipologia.exists' => 'La tipologia selezionata non è valida',
        ];
    }
}
