{{-- ======================================== --}}
{{-- DÉTAIL D'UN CANDIDAT                     --}}
{{-- ======================================== --}}
{{-- Accès : tout utilisateur connecté.              --}}
{{-- Variable reçue : $candidat (le candidat demandé). --}}

{{-- Numéro et nom complet du candidat --}}
N°{{$candidat->id}} | {{$candidat->fullname}} <br>


{{-- Lien de retour vers la liste des candidats --}}
<a href="{{Route('candidat.index')}}">Candidat</a>
