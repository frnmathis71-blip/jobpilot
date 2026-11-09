<x-layouts::jobpilot title="Fiche candidat" heading="Fiche candidat">
N°{{$candidat->id}} | {{$candidat->fullname}} <br>


<a href="{{Route('candidat.index')}}">Candidat</a>
</x-layouts::jobpilot>
