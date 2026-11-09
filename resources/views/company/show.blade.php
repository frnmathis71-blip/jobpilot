{{-- ======================================== --}}
{{-- DÉTAIL D'UNE ENTREPRISE                  --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin). --}}
{{-- Variable reçue : $company (l'entreprise demandée).       --}}

{{-- Nom de l'entreprise --}}
Nom de l'entreprise : {{$company->name}} <br>

{{-- Offres de l'entreprise, grâce à la relation offers() du modèle Company --}}
<section>
    <u>Offre proposé par l'entreprises : <br></u>
    @foreach($company->offers as $offer)
        Offre N°{{$offer->id}} : {{$offer->Name}} <br>
    @endforeach
</section>

{{-- Lien de retour vers la liste des entreprises --}}
Les <a href="{{route('company.index')}}">Entreprise</a>


{{-- Style CSS : bordure autour de la liste des offres --}}
<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>
