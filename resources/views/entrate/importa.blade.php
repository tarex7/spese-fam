@extends('layouts.app')
@if ($errors->any())
<div class="alert alert-danger rounded-0">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@section('content')
    <div class="container card mt-5 p-4">

        <h2 class="mb-5">Importa file entrate</h1>

            {!! Form::open(['url' => 'entrate/carica_file', 'files' => true]) !!}

            <div class="d-flex">
                {!! Form::file('excel_file') !!}
                {!! Form::number('anno', null, ['placeholder' => 'Inserisci anno','class' => 'form-control w-25']) !!}
                {!! Form::submit('Carica', ['class' => 'btn btn-primary mx-2']) !!}
            </div>

        {!! Form::close() !!}

    </div>
@endsection
