@extends('layouts.crud')

@section('titre', 'Détail')

@section('content')
    <h1>{{ $PARENT_SINGULIER->PARENT_CHAMP }}</h1>

    <h2>Ses PLURIEL</h2>
    <ul>
        @forelse ($PARENT_SINGULIER->PLURIEL as $SINGULIER)
            <li>
                <a href="{{ route('PLURIEL.show', $SINGULIER) }}">{{ $SINGULIER->CHAMP1 }}</a>
                ({{ $SINGULIER->CHAMP2 }})
            </li>
        @empty
            <li>Aucun.</li>
        @endforelse
    </ul>

    <a href="{{ route('PARENT_PLURIEL.edit', $PARENT_SINGULIER) }}">Modifier</a>

    <form action="{{ route('PARENT_PLURIEL.destroy', $PARENT_SINGULIER) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Supprimer aussi tous ses PLURIEL ?')">Supprimer</button>
    </form>

    <a href="{{ route('PARENT_PLURIEL.index') }}">Retour à la liste</a>
@endsection
