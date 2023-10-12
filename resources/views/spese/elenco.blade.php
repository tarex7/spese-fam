@extends('layouts.app', ['elem_id' => ''])
@section('content')
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
        @foreach($spese as $s)
        <tr>
            <th scope="row">{{$s->categoria}}</th>
            <th scope="row">{{$s->gen}}</th>
            <td>{{$s->feb}}</td>
            <td>{{$s->mar}}</td>
            <td>{{$s->apr}}</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
            <td>@mdo</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection
