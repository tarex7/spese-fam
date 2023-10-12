@extends('layouts.app', ['elem_id' => ''])
@section('content')
    {{-- <table class="table table-striped">
        <thead>
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
            </tr>
        </thead>
        <tbody>

            @foreach ($spese as $m => $s)
            @dd($s)
                <tr>
                    <th>{{$s}}</th>
                    <th></th>
                    <th></th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                </tr>
            @endforeach
        </tbody>
    </table> --}}
    <table class="table table-striped">
        <thead>
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
            </tr>
        </thead>
        <tbody>
            @foreach ($speseRaggruppate as $categoria => $importiMensili)
                <tr>
                    <td class="font-weight-bold">{{ $categoria }}</td>
                    @for ($mese = 1; $mese <= 12; $mese++)
                        <td>{{ $importiMensili[$mese] ?? 0 }}</td>
                    @endfor
                </tr>
            @endforeach
        </tbody>
    </table>
    
@endsection
