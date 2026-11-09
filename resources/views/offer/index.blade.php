{{-- ======================================== --}}
{{-- LISTE DES OFFRES                         --}}
{{-- ======================================== --}}
{{-- Accès : tout utilisateur connecté.                          --}}
{{-- Variable reçue : $offers (toutes les offres).               --}}
{{-- L'affichage change selon le rôle de l'utilisateur connecté. --}}


{{-- ======================================== --}}
{{-- PARTIE ADMINISTRATEUR ET ENTREPRISE      --}}
{{-- ======================================== --}}
{{-- ⚠ "Supprimer" est réservé à l'admin, "Modifier" et "Ajouter" aux entreprises : --}}
{{--   chaque rôle verra un bouton qui lui renvoie une erreur 403.                  --}}

@if (in_array(auth()->user()->role, ['admin', 'company']))

    {{-- Une carte <section> par offre --}}
    @foreach($offers as $offer)
        <section>
            {{-- Informations de l'offre --}}
            {{-- ⚠ $offer->name (minuscule) n'affiche rien : la colonne s'appelle "Name" --}}
            Offre N°{{$offer->id}} {{$offer->name}} <br>
            Salaire : {{$offer->Salary}} <br>
            Description : {{$offer->Description}} <br>
            {{-- Nom de l'entreprise, grâce à la relation company() du modèle Offer --}}
            Entreprise : {{$offer->company->name}} <br>

            {{-- Lien vers le détail de l'offre --}}
            <a href="{{route('offer.show', $offer)}}">Voir</a>

            {{-- Suppression : un lien ne peut pas envoyer de DELETE,      --}}
            {{-- on passe donc par un petit formulaire + @method('DELETE'). --}}
            {{-- confirm() affiche une fenêtre de confirmation avant l'envoi. --}}
            <form action="{{ route('offer.destroy', $offer) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                    onclick="return confirm('Confirmer la suppression ?')">
                    Supprimer
                </button>
            </form>

            {{-- Lien vers le formulaire de modification --}}
            <a href="{{route('offer.edit', $offer)}}">Modifier</a>
        </section>

    @endforeach

    {{-- Lien vers le formulaire de création --}}
    <a href="{{Route('offer.create')}}">Ajouter une offre</a>

    {{-- Style CSS : bordure autour de chaque offre --}}
    <style>
        section{
            border:1px solid black;
            margin:5px;
            padding:5px;
        }
    </style>
@endif


{{-- ======================================== --}}
{{-- PARTIE CANDIDAT                          --}}
{{-- ======================================== --}}
{{-- Lecture seule : uniquement le bouton "Voir". --}}

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

    {{-- Style CSS candidat --}}
    <style>
        section{
            border:1px solid black;
            margin:5px;
            padding:5px;
        }
    </style>
@endif
