@extends('layouts.app', ['elem_id' => ''])
@section('content')

    <elenco-component
    type = "spese" 
    :years_opt="{{ json_encode($years) }}" 
    :anno="{{ $anno }}" 
    getdataurl="/spese/getelenco"
    />

    
@endsection
