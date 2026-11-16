@extends('layouts.crud')

@section('titre', 'Modifier')

@section('content')
    <h1>Modifier « {{ $SINGULIER->CHAMP1 }} »</h1>

    <form action="{{ route('PLURIEL.update', $SINGULIER) }}" method="POST">
        @method('PUT')
        @include('PLURIEL._form')
        <button type="submit">Modifier</button>
    </form>

    <a href="{{ route('PLURIEL.index') }}">Retour à la liste</a>
@endsection
