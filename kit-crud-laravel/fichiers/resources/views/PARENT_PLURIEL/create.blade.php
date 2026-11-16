@extends('layouts.crud')

@section('titre', 'Ajouter')

@section('content')
    <h1>Ajouter</h1>

    <form action="{{ route('PARENT_PLURIEL.store') }}" method="POST">
        @include('PARENT_PLURIEL._form')
        <button type="submit">Enregistrer</button>
    </form>

    <a href="{{ route('PARENT_PLURIEL.index') }}">Retour à la liste</a>
@endsection
