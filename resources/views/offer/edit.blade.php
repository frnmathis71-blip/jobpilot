{{-- Formulaire de modification d'une offre (entreprises uniquement). --}}
{{-- Les champs sont pré-remplis avec les valeurs actuelles de l'offre. --}}
<form action="{{route('offer.update', $offer)}}" method="POST">
    @csrf 
    @method('PUT') {{-- un formulaire HTML ne sait faire que POST : on simule un PUT --}}
    <label>Nom de l'offre</label> <input type="text" name="Name" value="{{$offer->Name}}"> <br>
    <label>Salaire mensuel brut</label> <input type="number" name="Salary" value="{{$offer->Salary}}"> <br>
    <label>Entreprise</label> 
    <select name="company_id">
        @foreach($companies as $company)
            {{-- "selected" sur l'entreprise actuelle de l'offre --}}
            <option value="{{ $company->id }}"
                {{ $offer->company_id == $company->id ? 'selected' : '' }}>
                {{ $company->name }}
            </option>
        @endforeach
    </select> <br>
    <label>Description</label> <input type="text" name="Description" value="{{$offer->Description}}"> <br>
    <button type="submit">Modifier</button>
</form>