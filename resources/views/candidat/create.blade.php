{{-- ======================================== --}}
{{-- CRÉATION D'UN CANDIDAT                   --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin). --}}
{{-- À l'envoi : POST /candidat → CandidatController@store.   --}}

<form action="{{route('candidat.store')}}" method="POST">
    {{-- Jeton anti-CSRF obligatoire --}}
    @csrf

    {{-- Nom complet du candidat (champ "fullname" validé par le contrôleur) --}}
    <label>Prénom et nom : </label> <input type="text" name="fullname"> <br>

    {{-- Bouton d'envoi (⚠ le texte dit "Modifier" alors qu'on crée un candidat) --}}
    <button type="submit">Modifier</button>

</form>
