<form action="{{route('company.store')}}" method="POST">
    @csrf 
    <label>Nom</label> <input type="text" name="name">
    <button type="submit">Ajoutée</button>
</form>