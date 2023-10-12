@extends('layouts.app', ['elem_id' => ''])
@section('content')
    <div id="spese">

        <div class="row ">
            <div class="col-5"></div>
            <h1 class="text-center my-3">Spese</h1>

            <div class="col-12 d-flex  justify-content-end ">

                {{-- FILTRA --}}
                {!! Form::open(['url' => 'spese/filtra']) !!}
                <div class="d-flex float-right my-4">

                    <a class="btn btn-primary form-control mr-5" href={{ Route('spese/elenco') }}>Elenco</a>

                    {!! Form::select('anno', $years, $anno_sel, ['class' => 'form-control mx-1']) !!}

                    {!! Form::select('mese', $mesi, $mese_sel, ['class' => 'form-control  mx-3']) !!}

                    {!! Form::submit('Filtra', ['class' => 'btn btn-success mx-2']) !!}
                    <a class="btn btn-danger form-control" href={{ Route('spese') }}>Rimuovi</a>
                </div>

            </div>
        </div>
        {!! Form::close() !!}



        {{-- AGGIUNGI --}}

        {!! Form::open(['url' => 'spese/aggiungi']) !!}

        {!! Form::button('<i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi spesa', [
            'class' => 'btn btn-primary mb-3',
            'type' => 'submit',
        ]) !!}


        <div class="border bg-primary p-3 mb-3 rounded">
            <table class="table table-striped bg-light rounded ">
                <thead>
                    <tr>
                        <th></th>
                        {{-- <th scope="col">Nome</th> --}}
                        <th scope="col">Categoria</th>
                        <th scope="col">Data</th>
                        <th scope="col">Importo</th>
                        <th scope="col">Tipologia</th>
                    </tr>
                </thead>
                <tr>
                    <td></td>
                    {!! Form::hidden('spese_add', '') !!}

                    {{-- <td>{!! Form::text('nome_add', '', ['class' => 'form-control add']) !!}</td> --}}
                    <td>{!! Form::select('categorie_add', $cat, '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::date('data_add', '', ['class' => 'form-control add']) !!}</td>
                    <td>{!! Form::number('importo_add', '', ['class' => 'form-control add', 'step' => '0.01', 'min' => '0.01']) !!}</td>
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
                    {{-- <th scope="col">Nome</th> --}}
                    <th scope="col">Categoria</th>
                    <th scope="col">Data</th>
                    <th scope="col">Importo</th>
                    <th scope="col">Tipologia</th>
                </tr>
            </thead>
            <tbody id="spese">
                <tr>
                    <th></th>
                    {{-- <th scope="col"></th> --}}
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>

                @foreach ($spese as $s)
                
                    {{-- @php dd($spese_id ); echo('spese_id')@endphp --}}
                    <tr @if ($spese_id == $s->id) id="nome_add" @endif>
                        <td class="d-flex align-items-center justify-content-center">
                            <a @click="elimina" href={{ route('spese/elimina', $s->id) }}> <i
                                    class="fa-solid fa-trash mx-1 text-danger mt-2"></i></a>
                        </td>
                        {{-- {!! Form::hidden('spese[spesa_' . $s->id . '][id]', $s->id) !!} --}}

                        {{-- <td>{!! Form::text("spese[{$s->id}][nome]", $s->nome, ['class' => 'form-control']) !!}</td> --}}


                        <td>{!! Form::select("spese[{$s->id}][categorie]", $cat, $s->categorie_id, [
                            'class' => 'form-control',
                        ]) !!}</td>
                        <td>{!! Form::date("spese[{$s->id}][data]", $s->data, ['class' => 'form-control']) !!}</td>

                        <td>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">€</span>
                                </div>
                                {!! Form::number("spese[{$s->id}][importo]", number_format($s->importo, 2), [
                                    'class' => 'form-control',
                                    'step' => '0.01',
                                ]) !!}
                            </div>
                        </td>


                        <td>{!! Form::select("spese[{$s->id}][tipologia]", $tip, $s->tipologia_id, [
                            'class' => 'form-control',
                        ]) !!}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col">
                        Totale
                    </th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody id="spese">
                <tr>
                    <th scope="col" style="width:5%"></th>
                    <th scope="col" style="width:10%"></th>
                    <th scope="col" style="width:12.5%"></th>
                    <th scope="col"></th>
                    <th scope="col" style="width:25%">
                        <div class="input-group">

                            <div class="input-group-prepend">
                                <span class="input-group-text font-weight-bold">€</span>
                            </div>
                            {!! Form::text("totale", $totale, [
                                'class' => 'form-control font-weight-bold',
                                'step' => '0.01',
                                'disabled',
                            ]) !!}
                        </div>
                    </th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>

                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>

            </tbody>
        </table>

        {!! Form::submit('Salva', ['class' => 'btn btn-primary float-right mr-5 px-5']) !!}
        {!! Form::close() !!}

    </div>
@endsection
