@extends('layouts.app', ['elem_id' => ''])

{{--
@section('add')
<script src="{{ asset('js/add.js') }}" defer></script>
@endsection --}}

@section('content')
    <div id="spese">
        <h1 class="text-center my-3">Spese</h1>

        {{--  <a href="{{route('spese/aggiungi')}}"> <i id="add" class="fa-solid fa-square-plus fa-2x text-primary"></i> </a> --}}

        {{-- AGGIUNGI --}}

        {!! Form::open(['url' => 'spese/aggiungi']) !!}
        {!! Form::button('<i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi spesa', [
            'id' => 'addBtn',
            'class' => 'btn btn-primary mb-3',
            'type' => 'submit'
        ]) !!}


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

        {!! Form::open(['url' => 'spese/salva']) !!}
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
               
                    <tr  @if($spese_id == $s->id)  id="nome_add" @endif>
                        <td class="d-flex align-items-center justify-content-center">
                          
                            <a @click="elimina" href={{ route('spese/elimina', $s->id) }}> <i
                                    class="fa-solid fa-trash mx-1 text-danger mt-2"></i></a>
                        </td>
                        {{-- {!! Form::hidden('spese[spesa_' . $s->id . '][id]', $s->id) !!} --}}

                        <td>{!! Form::text("spese[{$s->id}][nome]", $s->nome, ['class' => 'form-control']) !!}</td>

                        <td>{!! Form::date("spese[{$s->id}][data]" , $s->data, ['class' => 'form-control']) !!}</td>

                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                {!! Form::number("spese[{$s->id}][importo]", number_format(intval($s->importo), 2), ['class' => 'form-control']) !!}
                            </div>
                        </td>
                        

                        <td>{!! Form::select("spese[{$s->id}][categorie]", $cat, $s->categorie_id, [
                            'class' => 'form-control',
                        ]) !!}</td>
                        <td>{!! Form::select("spese[{$s->id}][tipologia]", $tip, $s->tipologia_id, [
                            'class' => 'form-control',
                        ]) !!}</td> 
                    </tr>
                @endforeach

            </tbody>
        </table>
        {!! Form::submit('Salva', ['class' => 'btn btn-primary float-right']) !!}
        {!! Form::close() !!}

    </div>
@endsection
