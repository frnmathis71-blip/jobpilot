@extends('layouts.crud')

@section('titre', 'PLURIEL')

@section('content')
    <h1>Liste des PLURIEL</h1>

    <a href="{{ route('PLURIEL.create') }}">Ajouter</a>

    <table>
        <tr>
            <th>CHAMP1</th>
            <th>CHAMP2</th>
            <th>PARENT_SINGULIER</th>
            <th>Actions</th>
        </tr>
        @forelse ($PLURIEL as $SINGULIER)
            <tr>
                <td>{{ $SINGULIER->CHAMP1 }}</td>
                <td>{{ $SINGULIER->CHAMP2 }}</td>
                <td>{{ $SINGULIER->PARENT_SINGULIER?->PARENT_CHAMP }}</td>
                <td>
                    <a href="{{ route('PLURIEL.show', $SINGULIER) }}">Voir</a>
                    <a href="{{ route('PLURIEL.edit', $SINGULIER) }}">Modifier</a>
                    <form action="{{ route('PLURIEL.destroy', $SINGULIER) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4">Aucun élément.</td>
            </tr>
        @endforelse
    </table>
@endsection
