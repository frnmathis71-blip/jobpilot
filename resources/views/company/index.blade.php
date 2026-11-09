{{-- ======================================== --}}
{{-- LISTE DES ENTREPRISES                    --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin). --}}
{{-- Variable reçue : $companies (toutes les entreprises).    --}}

{{-- Une carte <section> par entreprise --}}
@foreach($companies as $company)
<section>
    {{-- Nom de l'entreprise --}}
    {{$company->name}} <br>

    {{-- Lien vers le détail (avec la liste de ses offres) --}}
    <a href="{{route('company.show', $company)}}">Voir</a>

    {{-- Suppression : formulaire DELETE avec fenêtre de confirmation. --}}
    {{-- Supprimer l'entreprise supprime aussi ses offres (cascade).  --}}
    <form action="{{ route('company.destroy', $company) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit"
            onclick="return confirm('Confirmer la suppression ?')">
            Supprimer
        </button>
    </form>

    {{-- Lien vers le formulaire de modification --}}
    <a href="{{route('company.edit', $company)}}">Modifier</a>
</section>


@endforeach

{{-- Lien vers le formulaire de création --}}
<a href="{{route('company.create')}}">Ajoutée une entreprise</a>

{{-- Style CSS : bordure autour de chaque entreprise --}}
<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>
