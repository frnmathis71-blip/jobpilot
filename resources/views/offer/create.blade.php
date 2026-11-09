<form action="{{route('offer.store')}}" method="POST">
    @csrf 

    <label>Nom de l'offre</label> <input type="text" name="Name"> <br>
    <label>Salaire mensuel brut</label> <input type="number" name="Salary"> <br>
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