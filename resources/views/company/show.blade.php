Nom de l'entreprise : {{$company->name}} <br>

<section>
    <u>Offre proposé par l'entreprises : <br></u>
    @foreach($company->offers as $offer)
        Offre N°{{$offer->id}} : {{$offer->Name}} <br>
    @endforeach
</section>

Les <a href="{{route('company.index')}}">Entreprise</a>


<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>