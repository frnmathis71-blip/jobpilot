{{-- Formulaire de modification d'une entreprise (admin), pré-rempli avec son nom. --}}
<form action="{{route('company.update', $company)}}" method="POST">
    @csrf
    @method('PUT')

    <label>Nom de l'entreprise </label> <input type="text" name="name" value="{{$company->name}}"> <br>
    <button type="submit">Modifier</button>

</form>