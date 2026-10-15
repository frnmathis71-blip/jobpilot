@foreach($candidats as $candidat)

<section>
    {{$candidat->fullname}} <br>


    <form action="{{ route('candidat.destroy', $candidat) }}" method="POST">
    @csrf
    @method('DELETE')
    <button type="submit"
        onclick="return confirm('Confirmer la suppression ?')">
        Supprimer
    </button>
    <a href="{{ route('candidat.show', $candidat)}}">Voir</a>
    <a href="{{ route('candidat.edit', $candidat)}}">Modifier</a>
</form>

</section>

@endforeach

<a href="{{ route('candidat.create', $candidat)}}">Ajouter un candidat</a>

<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>