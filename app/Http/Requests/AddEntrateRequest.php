<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddEntrateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'categorieentrate_add' => 'required|exists:categorie_entrate,id',
            'data_add' => 'required|date',
            'importo_add' => 'required|numeric',
            'tipologia_add' => 'required|exists:tipologia,id',
        ];
    }

    public function messages()
    {
        return [
            'categorieentrate_add.required' => 'Scegli una categoria',
            'categorieentrate_add.exists' => 'La categoria selezionata non è valida',
            'data_add.required' => 'Inserisci la data',
            'importo_add.required' => 'Inserisci un importo',
            'tipologia_add.required' => 'Scegli una tipologia',
            'tipologia_add.exists' => 'La tipologia selezionata non è valida',
        ];
    }
}
