<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditSpesaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'spese.*.categoriespese' => 'required|exists:categorie_spese,id',
            'spese.*.data' => 'required|date',
            'spese.*.importo' => 'required|numeric',
            'spese.*.tipologia' => 'required|exists:tipologia,id',
        ];
    }

    public function messages()
    {
        return [
            'spese.*.categorie.required' => 'Scegli una categoria per tutte le spese',
            'spese.*.categorie.exists' => 'La categoria selezionata non è valida',
            'spese.*.data.required' => 'Inserisci la data per tutte le spese',
            'spese.*.data.date' => 'La data deve essere una data valida',
            'spese.*.importo.required' => 'Inserisci l\'importo per tutte le spese',
            'spese.*.importo.numeric' => 'L\'importo deve essere un valore numerico',
            'spese.*.tipologia.required' => 'Scegli una tipologia per tutte le spese',
            'spese.*.tipologia.exists' => 'La tipologia selezionata non è valida',
        ];
    }
}
