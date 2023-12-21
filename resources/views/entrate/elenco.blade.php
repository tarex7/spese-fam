@extends('layouts.app', ['elem_id' => ''])
@section('content')
   
<elenco-component 
    type = "entrate" 
    :years_opt="{{ json_encode($years) }}" 
    :anno="{{ $anno }}" 
    getdataurl="/entrate/getelenco"
    />


@endsection
