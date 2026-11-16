@extends('layouts.crud')

@section('titre', 'Modifier')

@section('content')
    <h1>Modifier « {{ $PARENT_SINGULIER->PARENT_CHAMP }} »</h1>

    <form action="{{ route('PARENT_PLURIEL.update', $PARENT_SINGULIER) }}" method="POST">
        @method('PUT')
        @include('PARENT_PLURIEL._form')
        <button type="submit">Modifier</button>
    </form>

    <a href="{{ route('PARENT_PLURIEL.index') }}">Retour à la liste</a>
@endsection
