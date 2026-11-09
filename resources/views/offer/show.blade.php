{{-- ======================================== --}}
{{-- DÉTAIL D'UNE OFFRE                       --}}
{{-- ======================================== --}}
{{-- Accès : tout utilisateur connecté.         --}}
{{-- Variable reçue : $offer (l'offre demandée). --}}
{{-- ⚠ Seul le rôle candidat voit le contenu : admin et entreprise voient une page vide. --}}

@if (in_array(auth()->user()->role, ['candidat']))
    {{-- ⚠ $offer->name (minuscule) n'affiche rien : la colonne s'appelle "Name" --}}
    Offre N°{{$offer->id}} {{$offer->name}} <br>
    Salaire : {{$offer->Salary}} <br>
    Description : {{$offer->Description}} <br>
@endif
