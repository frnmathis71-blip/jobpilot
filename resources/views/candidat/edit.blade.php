{{-- ======================================== --}}
{{-- MODIFICATION D'UN CANDIDAT               --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin).    --}}
{{-- Variable reçue : $candidat (le candidat à modifier).        --}}
{{-- À l'envoi : PUT /candidat/{id} → CandidatController@update. --}}

<form action="{{route('candidat.update', $candidat)}}" method="POST">
    {{-- Jeton anti-CSRF obligatoire --}}
    @csrf
    {{-- Le formulaire est envoyé en POST mais traité comme un PUT --}}
    @method('PUT')

    {{-- Champ pré-rempli avec le nom actuel --}}
    <label>Prénom et nom : </label> <input type="text" name="fullname" value="{{$candidat->fullname}}"> <br>

    {{-- Bouton d'envoi (⚠ le texte dit "Ajouter" alors qu'on modifie un candidat) --}}
    <button type="submit">Ajouter</button>

</form>
