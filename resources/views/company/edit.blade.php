<x-layouts::jobpilot title="Modifier l'entreprise" heading="Modifier l'entreprise">
<form action="{{route('company.update', $company)}}" method="POST">
    @csrf
    @method('PUT')

    <label>Nom de l'entreprise </label> <input type="text" name="name" value="{{$company->name}}"> <br>
    <button type="submit">Modifier</button>

</form>
</x-layouts::jobpilot>
