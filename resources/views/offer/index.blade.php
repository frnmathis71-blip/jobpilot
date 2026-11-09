{{-- Liste des offres. L'affichage dépend du rôle de l'utilisateur connecté. --}}

{{-- Admin et entreprise : liste + boutons Voir / Supprimer / Modifier / Ajouter --}}
{{-- ⚠ Supprimer est réservé à l'admin, Modifier/Ajouter aux entreprises : --}}
{{--   l'autre rôle obtiendra une erreur 403 en cliquant. --}}
@if (in_array(auth()->user()->role, ['admin', 'company']))
    @foreach($offers as $offer)
        <section>
            Offre N°{{$offer->id}} {{$offer->name}} <br>
            Salaire : {{$offer->Salary}} <br>
            Description : {{$offer->Description}} <br>
            Entreprise : {{$offer->company->name}} <br>
            <a href="{{route('offer.show', $offer)}}">Voir</a>
        

            {{-- Suppression : formulaire DELETE avec confirmation --}}
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
@endif

{{-- Candidat : liste en lecture seule --}}
@if (in_array(auth()->user()->role, ['candidat']))
    @foreach($offers as $offer)
        <section>
            Offre N°{{$offer->id}} {{$offer->name}} <br>
            Salaire : {{$offer->Salary}} <br>
            Description : {{$offer->Description}} <br>
            Entreprise : {{$offer->company->name}} <br>
            <a href="{{route('offer.show', $offer)}}">Voir</a>
        </section>
        
    @endforeach

    <style>
        section{
            border:1px solid black;
            margin:5px;
            padding:5px;
        }
    </style>
@endif