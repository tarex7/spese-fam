@extends('layouts.app')
@section('content')
    <div id="spese" class="d-flex justify-content-center w-100 border"
        @if (isset($categorie_entrate_id)) data-categoria_id="$categoria_id" @endif>


        <div class="w-75">
            {!! Form::open([
                'url' => 'categorie_entrate/aggiungi',
                'id' => isset($categorie_entrate_id) ? $categorie_entrate_id : null,
            ]) !!}

            <h1 class="text-center my-3">Categorie entrate</h1>

            <div class="bg-primary p-4">

                <table class="table table-striped bg-light rounded">
                    {!! Form::button('<i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi categoria', [
                        'id' => 'addBtn',
                        'class' => 'btn btn-light mb-3',
                        'type' => 'submit',
                    ]) !!}
                    <tr>
                        <th style="width:12%"></th>
                        <th>
                            <label>Nome</label>
                            {!! Form::text('nome_add', '', ['class' => 'form-control add w-50']) !!}
                            </td>
                        </th>
                    </tr>
                </table>

                {!! Form::close() !!}


            </div>
            {!! Form::open(['url' => 'categorie_entrate/salva']) !!}

            <table class="table table-striped ">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Nome</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorie_entrate as $s)
                        <tr @if (isset($categorie_entrate_id) && $categorie_entrate_id == $s->id) id="nome_add" @endif>
                            <td class="d-flex align-items-center justify-content-end">

                                <a @click="elimina" href={{ route('categorie_entrate/elimina', $s->id) }}> <i
                                        class="fa-solid fa-trash mx-1 text-danger mt-2"></i>
                                </a>
                            </td>

                            <td>{!! Form::text("categorie_entrate[{$s->id}]", $s->nome, ['class' => 'form-control']) !!}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            {!! Form::submit('Modifica', ['class' => 'btn btn-primary float-right  px-5 mb-5']) !!}
            {!! Form::close() !!}
        </div>

    </div>
@endsection
