{{-- Formulaire de création d'une offre (entreprises uniquement). --}}
{{-- Envoyé en POST vers offer.store → OfferController@store. --}}
<form action="{{route('offer.store')}}" method="POST">
    @csrf {{-- jeton de sécurité obligatoire pour tout formulaire POST --}}

    <label>Nom de l'offre</label> <input type="text" name="Name"> <br>
    <label>Salaire mensuel brut</label> <input type="number" name="Salary"> <br>
    {{-- Menu déroulant : une option par entreprise, la valeur envoyée est son id --}}
    <label>Entreprise</label> 
    <select name="company_id">
        @foreach($companies as $company)
            <option value="{{$company->id}}">
                {{$company->name}}
            </option>
        @endforeach
    </select> <br>
    <label>Description</label> <input type="text" name="Description"> <br>
    <button type="submit">Ajouter</button>
</form>