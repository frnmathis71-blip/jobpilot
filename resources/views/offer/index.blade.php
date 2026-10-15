@foreach($offers as $offer)
    <section>
        Offre N°{{$offer->id}} {{$offer->name}} <br>
        Salaire : {{$offer->Salary}} <br>
        Description : {{$offer->Description}} <br>
        Entreprise : {{$offer->company->name}} <br>
        <a href="{{route('offer.show', $offer)}}">Voir</a>
    

        <form action="{{ route('offer.destroy', $offer) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit"
                onclick="return confirm('Confirmer la suppression ?')">
                Supprimer
            </button>
        </form>

        <a href="{{route('offer.edit', $offer)}}">Modifier</a>
    </section>
    
@endforeach


<a href="{{Route('offer.create')}}">Ajouter une offre</a>
<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>