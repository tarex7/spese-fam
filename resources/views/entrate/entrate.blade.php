@extends('layouts.app', ['elem_id' => ''])
@section('content')

    <div id="entrate">
        @if ($errors->any())
        <div class="alert alert-danger rounded-0">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <budget-form-component 
        title="Entrate"
        url="/entrate/filtra"

        :anno="{{ $anno }}"
        :mese="{{ $mese }}" 
        :cat_opt="{{ json_encode($cat) }}"
        :months_opt="{{ json_encode($mesi) }}" 
        :years_opt="{{ json_encode($years) }}" 
        :tip_opt="{{ json_encode($tip) }}"
        :data="{{ json_encode($entrate) }}" 
        :totale="{{ $totale }}" />

        
@endsection
