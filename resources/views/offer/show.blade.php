{{-- Détail d'une offre. --}}
{{-- ⚠ Seul le rôle candidat voit le contenu ; admin et entreprise voient une page vide. --}}
@if (in_array(auth()->user()->role, ['candidat']))
    Offre N°{{$offer->id}} {{$offer->name}} <br>
    Salaire : {{$offer->Salary}} <br>
    Description : {{$offer->Description}} <br>
@endif
