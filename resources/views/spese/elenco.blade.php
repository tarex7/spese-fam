@extends('layouts.app', ['elem_id' => ''])
@section('content')
   <h1 class="text-center my-3">Elenco spese</h1>
   <div class="float-right mx-1 mb-4 d-flex">

    {!! Form::open(['url' => 'spese/elenco']) !!}
   <div class="d-flex">
    {!! Form::select('anno', $years, $anno_sel, ['class' => 'form-control mx-1 w-100']) !!}
    {!! Form::submit('Filtra', ['class' => 'btn btn-success mx-2']) !!}
    <a class="btn btn-danger form-control" href={{ Route('spese/elenco',) }}>Rimuovi</a>
   </div>

   </div>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Spesa</th>
                <th scope="col">GEN</th>
                <th scope="col">FEB</th>
                <th scope="col">MAR</th>
                <th scope="col">APR</th>
                <th scope="col">MAG</th>
                <th scope="col">GIU</th>
                <th scope="col">LUG</th>
                <th scope="col">AGO</th>
                <th scope="col">SET</th>
                <th scope="col">OTT</th>
                <th scope="col">NOV</th>
                <th scope="col">DIC</th>
                <th scope="col" class="bg-secondary">TOT</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($speseRaggruppate as $categoria => $importiMensili)
           
                <tr>
                    <td  class="font-weight-bold">{{ $categoria }}</td>
                    @for ($mese = 1; $mese <= 12; $mese++)
                        <td>{{ $importiMensili[$mese] ?? 0 }} €</td>
                    @endfor
                    <td class="font-weight-bold bg-dark text-danger">{{array_sum($importiMensili)}} €</td>
                </tr>
            @endforeach
           <thead class="bg-primary text-white">
            <tr>
                <td>
                    Totale
                </td>
                @foreach ($spese_mensili as $sp)
                    <td>
                        {{ $sp }} €
                    </td>
                @endforeach
                <td class="font-weight-bold bg-dark text-danger">{{array_sum($spese_mensili)}} €</td>
            </tr>
           </thead>
        </tbody>
    </table>
@endsection
