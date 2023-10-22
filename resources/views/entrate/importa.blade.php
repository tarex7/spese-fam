@extends('layouts.app')

@section('content')
    <div class="container mt-5 card p-4">

        <h2 class="mb-5">Importa file entrate </h1>

            {!! Form::open(['url' => 'spese/carica_file', 'files' => true]) !!}

            <div class="d-flex">
                {!! Form::file('excel_file') !!}
                {!! Form::number('anno', null, ['placeholder' => 'Inserisci anno','class' => 'form-control w-25']) !!}
                {!! Form::submit('Carica', ['class' => 'btn btn-primary mx-2']) !!}
            </div>

        {!! Form::close() !!}

    </div>
@endsection
