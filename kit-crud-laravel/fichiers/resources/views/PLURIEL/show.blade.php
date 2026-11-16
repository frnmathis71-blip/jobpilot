@extends('layouts.crud')

@section('titre', 'Détail')

@section('content')
    <h1>{{ $SINGULIER->CHAMP1 }}</h1>

    <p>CHAMP2 : {{ $SINGULIER->CHAMP2 }}</p>
    <p>PARENT_SINGULIER : {{ $SINGULIER->PARENT_SINGULIER?->PARENT_CHAMP }}</p>

    <a href="{{ route('PLURIEL.edit', $SINGULIER) }}">Modifier</a>

    <form action="{{ route('PLURIEL.destroy', $SINGULIER) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
    </form>

    <a href="{{ route('PLURIEL.index') }}">Retour à la liste</a>
@endsection
