<x-layouts::jobpilot title="Candidatures">
{{-- Candidatures reçues sur les offres de l'entreprise connectée --}}
<h1>Candidatures reçues</h1>

@forelse($offers as $offer)
    <section>
        <strong>Offre N°{{$offer->id}} : {{$offer->Name}}</strong> <br>

        @forelse($offer->jobposting as $jobposting)
            - {{$jobposting->users->name ?? 'Candidat supprimé'}}
            ({{$jobposting->users->email ?? ''}})
            le {{$jobposting->created_at->format('d/m/Y')}} <br>
        @empty
            Aucune candidature pour le moment. <br>
        @endforelse
    </section>
@empty
    <p>Vous n'avez publié aucune offre.</p>
@endforelse

<a href="{{route('offer.index')}}">Retour aux offres</a>

<style>
    section{
        border:1px solid black;
        margin:5px;
        padding:5px;
    }
</style>
</x-layouts::jobpilot>
