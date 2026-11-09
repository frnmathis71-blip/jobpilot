{{-- ======================================== --}}
{{-- CRÉATION D'UNE ENTREPRISE                --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin). --}}
{{-- À l'envoi : POST /company → CompanyController@store.      --}}

<form action="{{route('company.store')}}" method="POST">
    {{-- Jeton anti-CSRF obligatoire --}}
    @csrf

    {{-- Nom de l'entreprise (champ "name" validé par le contrôleur) --}}
    <label>Nom</label> <input type="text" name="name">

    {{-- Bouton d'envoi --}}
    <button type="submit">Ajoutée</button>
</form>
