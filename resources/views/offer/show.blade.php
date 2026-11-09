<x-layouts::jobpilot title="Détail de l'offre" heading="Détail de l'offre">
{{-- ======================================== --}}
{{-- PARTIE CANDIDAT --}}
{{-- ======================================== --}}
@if (in_array(auth()->user()->role, ['candidat']))
    Offre N°{{$offer->id}} {{$offer->name}} <br>
    Salaire : {{$offer->Salary}} <br>
    Description : {{$offer->Description}} <br>
@endif
</x-layouts::jobpilot>
