{{-- ======================================== --}}
{{-- CRÉATION D'UNE OFFRE                     --}}
{{-- ======================================== --}}
{{-- Accès : entreprises uniquement (middleware IsCompany).            --}}
{{-- Variable reçue du contrôleur : $companies (toutes les entreprises). --}}
{{-- À l'envoi : POST /offer → OfferController@store.                  --}}

<form action="{{route('offer.store')}}" method="POST">
    {{-- Jeton anti-CSRF : obligatoire dans tout formulaire POST, sinon erreur 419 --}}
    @csrf

    {{-- Chaque "name" doit correspondre à une colonne de la table offers --}}
    {{-- (Name, Salary, Description, company_id) : c'est ce que valide le contrôleur. --}}

    {{-- Titre de l'offre --}}
    <label>Nom de l'offre</label> <input type="text" name="Name"> <br>

    {{-- Salaire : champ numérique --}}
    <label>Salaire mensuel brut</label> <input type="number" name="Salary"> <br>

    {{-- Entreprise : menu déroulant avec une option par entreprise. --}}
    {{-- Le texte affiché est le nom, la valeur envoyée est l'id.     --}}
    <label>Entreprise</label>
    <select name="company_id">
        @foreach($companies as $company)
            <option value="{{$company->id}}">
                {{$company->name}}
            </option>
        @endforeach
    </select> <br>

    {{-- Description de l'offre --}}
    <label>Description</label> <input type="text" name="Description"> <br>

    {{-- Bouton d'envoi du formulaire --}}
    <button type="submit">Ajouter</button>
</form>
