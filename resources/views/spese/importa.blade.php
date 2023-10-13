@extends('layouts.app')

@section('content')
    <div class="container">

        <h2 class="mb-5">Carica un file da importare</h1>

            {{-- <form action="{{ route('spese/carica_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="ods_file">
                <button class="btn btn-primary" type="submit">Carica</button>
            </form> --}}

            {!! Form::open(['url' => 'spese/carica_file', 'files' => true]) !!}

          <div class="d-flex">
            {!! Form::file('excel_file') !!}
            {!! Form::number('anno', null, ['placeholder' => 'Inserisci anno','class' => 'form-control w-25']) !!}
            {!! Form::submit('Carica', ['class' => 'btn btn-primary mx-2']) !!}
          </div>

            {!! Form::close() !!}

    </div>
@endsection
