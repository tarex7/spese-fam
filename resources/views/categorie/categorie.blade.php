@extends('layouts.app')
@section('content')
    <div id="spese" class="d-flex justify-content-center w-100 border"
        @if (isset($categorie_id)) data-categoria_id="$categoria_id" @endif>


        <div class="w-75">
            {!! Form::open([
                'url' => 'categorie/aggiungi',
                'id' => isset($categorie_id) ? $categorie_id : null,
            ]) !!}




            <h1 class="text-center my-3">Categorie</h1>
            {!! Form::button('<i class="fa-solid fa-square-plus mr-2 fa-lg"></i> Aggiungi categoria', [
                'id' => 'addBtn',
                'class' => 'btn btn-primary mb-3',
                'type' => 'submit',
            ]) !!}
            <div class="bg-primary p-4">

                <table class="table table-striped bg-light rounded">

                    <tr>
                        <th></th>
                        <th>
                            <label>Nome</label>
                            {!! Form::text('nome_add', '', ['class' => 'form-control add w-50']) !!}
                            </td>
                        </th>
                    </tr>
                </table>

                {!! Form::close() !!}


            </div>
            {!! Form::open(['url' => 'categorie/salva']) !!}

            <table class="table table-striped ">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Nome</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorie as $s)
                        <tr @if (isset($categorie_id) && $categorie_id == $s->id) id="nome_add" @endif>
                            <td class="d-flex align-items-center justify-content-end">

                                <a @click="elimina" href={{ route('categorie/elimina', $s->id) }}> <i
                                        class="fa-solid fa-trash mx-1 text-danger mt-2"></i>
                                </a>
                            </td>
                            {{-- {!! Form::hidden('categorie[spesa_' . $s->id . '][id]', $s->id) !!} --}}

                            <td>{!! Form::text("categorie[{$s->id}]", $s->nome, ['class' => 'form-control']) !!}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
            {!! Form::submit('Salva', ['class' => 'btn btn-primary float-right']) !!}
            {!! Form::close() !!}
        </div>

    </div>
@endsection
