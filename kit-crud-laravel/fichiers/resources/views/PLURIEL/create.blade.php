@extends('layouts.crud')

@section('titre', 'Ajouter')

@section('content')
    <h1>Ajouter</h1>

    <form action="{{ route('PLURIEL.store') }}" method="POST">
        @include('PLURIEL._form')
        <button type="submit">Enregistrer</button>
    </form>

    <a href="{{ route('PLURIEL.index') }}">Retour à la liste</a>
@endsection
