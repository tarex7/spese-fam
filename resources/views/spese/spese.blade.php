@extends('layouts.app', ['elem_id' => ''])

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

        {{-- AGGIUNGI --}}

        {!! Form::open(['url' => 'spese/aggiungi']) !!}
        {!! Form::submit('Aggiungi spesa', ['id' => 'addBtn', 'class' => 'btn btn-primary mb-1']) !!}

        <div class="border bg-primary p-3 mb-3 rounded">
            <table class="table table-striped bg-light rounded ">
                <thead>
                    <tr>
                        <th></th> 
                        <th scope="col">Nome</th>
                        <th scope="col">Data</th>
                        <th scope="col">Importo</th>
                        <th scope="col">Categoria</th>
                        <th scope="col">Tipologia</th>
                    </tr>
                </thead>
                <tr>
                    <td> </td>
                    {!! Form::hidden('spese_add', '') !!}

                    <td>{!! Form::text('nome_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::date('data_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::number('importo_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::select('categorie_add', $cat, '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::select('tipologia_add', $tip, '', ['class' => 'form-control add']) !!}</td>
                </tr>
            </table>
        </div>
        {!! Form::close() !!}

        {{-- SALVA --}}

        {!! Form::open(['url' => 'spese/modifica']) !!}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">Nome</th>
                    <th scope="col">Data</th>
                    <th scope="col">Importo</th>
                    <th scope="col">Categoria</th>
                    <th scope="col">Tipologia</th>
                </tr>
            </thead>
            <tbody id="spese">
                @foreach ($spese as $s)
                    @php $s->importo .= ' €';   @endphp
                    <tr>
                        <td>
                           
                            <a @click="elimina" href={{ route('spese/elimina', $s->id) }}> <i
                                    class="fa-solid fa-trash mx-1 text-danger"></i></a>
                        </td>
                        {!! Form::hidden('spese[spesa_' . $s->id . '][id]', $s->id) !!}

                        <td>{!! Form::text('spese[spesa_' . $s->id . '][nome]', $s->nome, ['class' => 'form-control']) !!}</td>
                        <td>{!! Form::date('spese[spesa_' . $s->id . '][data]', $s->data, ['class' => 'form-control']) !!}</td>

                        <td>{!! Form::number('spese[spesa_' . $s->id . '][importo]', number_format(intval($s->importo), 2), [
                            'class' => 'form-control',
                        ]) !!}</td>

                        <td>{!! Form::select('spese[spesa_' . $s->id . '][categorie_id]', $cat, $s->categorie_id, [
                            'class' => 'form-control',
                        ]) !!}</td>
                        <td>{!! Form::select('spese[spesa_' . $s->id . '][tipologia_id]', $tip, $s->tipologia_id, [
                            'class' => 'form-control',
                        ]) !!}</td>



                    </tr>
                @endforeach

            </tbody>
        </table>
        {!! Form::submit('Salva', ['class' => 'btn btn-primary']) !!}
        {!! Form::close() !!}

    </div>
@endsection
