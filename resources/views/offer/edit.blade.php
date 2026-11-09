{{-- ======================================== --}}
{{-- MODIFICATION D'UNE OFFRE                 --}}
{{-- ======================================== --}}
{{-- Accès : entreprises uniquement (middleware IsCompany).               --}}
{{-- Variables reçues : $offer (l'offre à modifier), $companies (la liste). --}}
{{-- À l'envoi : PUT /offer/{id} → OfferController@update.                --}}

<form action="{{route('offer.update', $offer)}}" method="POST">
    {{-- Jeton anti-CSRF obligatoire --}}
    @csrf
    {{-- Un formulaire HTML ne connaît que GET et POST :           --}}
    {{-- @method('PUT') indique à Laravel de le traiter comme un PUT. --}}
    @method('PUT')

    {{-- Les attributs "value" pré-remplissent les champs avec les données actuelles --}}

    {{-- Titre de l'offre --}}
    <label>Nom de l'offre</label> <input type="text" name="Name" value="{{$offer->Name}}"> <br>

    {{-- Salaire --}}
    <label>Salaire mensuel brut</label> <input type="number" name="Salary" value="{{$offer->Salary}}"> <br>

    {{-- Entreprise : on ajoute "selected" sur l'entreprise actuelle de l'offre --}}
    {{-- pour qu'elle soit déjà choisie dans le menu déroulant.               --}}
    <label>Entreprise</label>
    <select name="company_id">
        @foreach($companies as $company)
            <option value="{{ $company->id }}"
                {{ $offer->company_id == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select> <br>

    {{-- Description --}}
    <label>Description</label> <input type="text" name="Description" value="{{$offer->Description}}"> <br>

    {{-- Bouton d'envoi --}}
    <button type="submit">Modifier</button>
</form>
