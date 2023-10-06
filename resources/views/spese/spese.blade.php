@extends('layouts.default')
{{--
@section('add')
<script src="{{ asset('js/add.js') }}" defer></script>
@endsection --}}
@if (session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif
@section('content')
   
    <div id="spese">
        <h1 class="text-center my-3">Spese</h1>

        {{--  <a href="{{route('spese/aggiungi')}}"> <i id="add" class="fa-solid fa-square-plus fa-2x text-primary"></i> </a> --}}

        {!! Form::open(['url' => 'spese/aggiungi']) !!}
        {!! Form::submit('Aggiungi spesa', ['id' => 'addBtn' ,'class' => 'btn btn-primary mb-1']) !!}

        <div class="border bg-primary p-3 mb-3">
            <table class="table table-striped bg-light ">
                <thead>
                    <tr>
                        <th></th>
                        <th scope="col">Data</th>
                        <th scope="col">Importo</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Tipologia</th>
                    </tr>
                </thead>
                <tr>
                    <td> </td>
                    {!! Form::hidden('spese_add', '') !!}

                    <td>{!! Form::date('data_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::number('importo_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::select('categorie_add', $cat, '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::select('tipologia_add', $tip, '', ['class' => 'form-control add']) !!}</td>
                </tr>
            </table>
        </div>
        {!! Form::close() !!}
        {{-- <datatable-component 
                           info="{{ $spese }}"
                           cat_options="{{json_encode($cat)}}"
                           tip_options="{{json_encode($tip)}}">
    
            </datatable-component> --}}

        {!! Form::open(['url' => 'spese/salva']) !!}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">Data</th>
                    <th scope="col">Importo</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Tipologia</th>
                </tr>
            </thead>
            <tbody id="spese_div">
                @foreach ($spese as $s)
                    <tr>
                        <td> <i class="fa-solid fa-pencil text-secondary"></i>
                            <i class="fa-solid fa-trash mx-1 text-danger"></i>
                        </td>
                        {!! Form::hidden('spese[]', $s->id) !!}
                        <td>{!! Form::date('data[]', $s->data, ['class' => 'form-control']) !!}</td>
                        <td>{!! Form::number('importo[]', $s->importo, ['class' => 'form-control']) !!}</td>

                        <td>{!! Form::select('categorie[]', $cat, $s->categorie_id, ['class' => 'form-control']) !!}</td>
                        <td>{!! Form::select('tipologia[]', $tip, $s->tipologia_id, ['class' => 'form-control']) !!}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        {!! Form::submit('Salva', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}

    </div>
@endsection
