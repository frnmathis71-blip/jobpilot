{{-- Récapitulatif de l'offre --}}
<h1>Postuler à l'offre N°{{$offer->id}}</h1>

<section>
    Nom : {{$offer->Name}} <br>
    Salaire : {{$offer->Salary}} <br>
    Description : {{$offer->Description}} <br>
    Entreprise : {{$offer->company->user->name ?? ''}} <br>
</section>

{{-- Formulaire de candidature : envoie l'id de l'offre vers JobPostingController@store --}}
<form action="{{route('jobposting.store')}}" method="POST">
    @csrf
    <input type="hidden" name="offer_id" value="{{$offer->id}}">
    <button type="submit">Postuler</button>
</form>

<a href="{{route('offer.index')}}">Retour aux offres</a>

<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>
