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
</form>

</section>

@endforeach

<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>