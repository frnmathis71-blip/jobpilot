<form action="{{route('candidat.update', $candidat)}}" method="POST">
    @csrf
    @method('PUT')

    <label>Prénom et nom : </label> <input type="text" name="fullname" value="{{$candidat->fullname}}"> <br>
    <button type="submit">Ajouter</button>

</form>