@if (in_array(auth()->user()->role, ['candidat']))
    Offre N°{{$offer->id}} {{$offer->name}} <br>
    Salaire : {{$offer->Salary}} <br>
    Description : {{$offer->Description}} <br>
@endif
