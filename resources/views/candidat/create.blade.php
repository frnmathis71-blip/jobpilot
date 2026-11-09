{{-- Formulaire de création d'un candidat (admin). Envoyé vers CandidatController@store. --}}
<form action="{{route('candidat.store')}}" method="POST">
    @csrf

    <label>Prénom et nom : </label> <input type="text" name="fullname"> <br>
    <button type="submit">Modifier</button>

</form>