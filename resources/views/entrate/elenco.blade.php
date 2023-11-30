@extends('layouts.app', ['elem_id' => ''])
@section('content')
    <!--    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            @breadcrumbs
        </ol>
    </nav>-->


    <h1 class="text-center my-3">Elenco entrate</h1>
    <div class="float-right mx-1 mb-4 d-flex">

        {!! Form::open(['url' => 'entrate/elenco']) !!}
        <div class="d-flex">
            {!! Form::select('anno', $years, $anno_sel, ['class' => 'form-control mx-1 w-100']) !!}
            {!! Form::submit('Filtra', ['class' => 'btn btn-success mx-2']) !!}
            <a class="btn btn-danger form-control" href={{ Route('entrate/elenco') }}>Rimuovi</a>
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
        @foreach ($entrateRaggruppate as $categoria => $importiMensili)

            <tr>
                <td  class="font-weight-bold bg-secondary text-white">{{ $categoria }}</td>
                @for ($mese = 1; $mese <= 12; $mese++)
                    <td class="@if($importiMensili[$mese] == 0) text-muted @endif">

                        {{ number_format($importiMensili[$mese],2,',','.') ?? 0 }} €
                    </td>
                @endfor
                <td class="font-weight-bold bg-warning text-dark">{{number_format(array_sum($importiMensili),2,',','.')}} €</td>
            </tr>
        @endforeach
        <thead class="bg-secondary text-white">
        <tr>
            <td>
                Totale
            </td>
            @foreach ($entrate_mensili as $sp)
                <td >
                    {{--  --}}
                    {{ $sp }} €
                </td>
            @endforeach
            <td  class="font-weight-bold bg-success  text-white">{{number_format(array_sum($entrate_mensili),2,',','.')}} €</td>
        </tr>
        </thead>
        </tbody>
    </table>
@endsection