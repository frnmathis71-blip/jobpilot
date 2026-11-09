{{-- ======================================== --}}
{{-- MODIFICATION D'UNE ENTREPRISE            --}}
{{-- ======================================== --}}
{{-- Accès : administrateurs uniquement (middleware IsAdmin).  --}}
{{-- Variable reçue : $company (l'entreprise à modifier).      --}}
{{-- À l'envoi : PUT /company/{id} → CompanyController@update. --}}

<form action="{{route('company.update', $company)}}" method="POST">
    {{-- Jeton anti-CSRF obligatoire --}}
    @csrf
    {{-- Le formulaire est envoyé en POST mais traité comme un PUT --}}
    @method('PUT')

    {{-- Champ pré-rempli avec le nom actuel --}}
    <label>Nom de l'entreprise </label> <input type="text" name="name" value="{{$company->name}}"> <br>

    {{-- Bouton d'envoi --}}
    <button type="submit">Modifier</button>

</form>
