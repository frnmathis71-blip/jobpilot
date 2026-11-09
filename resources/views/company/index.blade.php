@foreach($companies as $company)
<section>
    {{$company->name}} <br>

    <a href="{{route('company.show', $company)}}">Voir</a>
    <form action="{{ route('company.destroy', $company) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit"
            onclick="return confirm('Confirmer la suppression ?')">
            Supprimer
        </button>
    </form>
    <a href="{{route('company.edit', $company)}}">Modifier</a>
</section>


@endforeach

<a href="{{route('company.create')}}">Ajoutée une entreprise</a>

<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>