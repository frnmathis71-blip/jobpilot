@extends('layouts.crud')

@section('titre', 'PARENT_PLURIEL')

@section('content')
    <h1>Liste des PARENT_PLURIEL</h1>

    <a href="{{ route('PARENT_PLURIEL.create') }}">Ajouter</a>

    <table>
        <tr>
            <th>PARENT_CHAMP</th>
            <th>Nombre de PLURIEL</th>
            <th>Actions</th>
        </tr>
        @forelse ($PARENT_PLURIEL as $PARENT_SINGULIER)
            <tr>
                <td>{{ $PARENT_SINGULIER->PARENT_CHAMP }}</td>
                <td>{{ $PARENT_SINGULIER->PLURIEL->count() }}</td>
                <td>
                    <a href="{{ route('PARENT_PLURIEL.show', $PARENT_SINGULIER) }}">Voir</a>
                    <a href="{{ route('PARENT_PLURIEL.edit', $PARENT_SINGULIER) }}">Modifier</a>
                    <form action="{{ route('PARENT_PLURIEL.destroy', $PARENT_SINGULIER) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Supprimer aussi tous ses PLURIEL ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Aucun élément.</td>
            </tr>
        @endforelse
    </table>
@endsection
