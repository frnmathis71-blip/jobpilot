<x-layouts::jobpilot title="Tableau de bord">

    @auth
        @php $role = auth()->user()->role; @endphp

        {{-- ======================================== --}}
        {{-- PARTIE ADMINISTRATEUR --}}
        {{-- ======================================== --}}
        @if ($role === 'admin')
            <div class="jp-hero">
                <h1>Administration</h1>
                <p>Gérez les candidats, les entreprises et les offres de la plateforme.</p>
            </div>

            <div class="jp-grid">
                <a href="{{ route('candidat.index') }}" class="jp-card">
                    <span class="jp-card-tag">Gestion</span>
                    <h2>Candidats</h2>
                    <p>Ajouter, modifier ou supprimer des candidats.</p>
                </a>
                <a href="{{ route('company.index') }}" class="jp-card">
                    <span class="jp-card-tag">Gestion</span>
                    <h2>Entreprises</h2>
                    <p>Ajouter, modifier ou supprimer des entreprises.</p>
                </a>
                <a href="{{ route('offer.index') }}" class="jp-card">
                    <span class="jp-card-tag">Modération</span>
                    <h2>Offres</h2>
                    <p>Consulter et supprimer les offres publiées.</p>
                </a>
            </div>

        {{-- ======================================== --}}
        {{-- PARTIE ENTREPRISE --}}
        {{-- ======================================== --}}
        @elseif ($role === 'company')
            <div class="jp-hero">
                <h1>Espace entreprise</h1>
                <p>Publiez vos offres et suivez les candidatures reçues.</p>
            </div>

            <div class="jp-grid">
                <a href="{{ route('offer.create') }}" class="jp-card">
                    <span class="jp-card-tag">Recruter</span>
                    <h2>Publier une offre</h2>
                    <p>Créer une nouvelle offre d'emploi.</p>
                </a>
                <a href="{{ route('offer.index') }}" class="jp-card">
                    <span class="jp-card-tag">Offres</span>
                    <h2>Mes offres</h2>
                    <p>Consulter et modifier les offres publiées.</p>
                </a>
                <a href="{{ route('jobposting.index') }}" class="jp-card">
                    <span class="jp-card-tag">Suivi</span>
                    <h2>Candidatures</h2>
                    <p>Voir qui a postulé à vos offres.</p>
                </a>
            </div>

        {{-- ======================================== --}}
        {{-- PARTIE CANDIDAT --}}
        {{-- ======================================== --}}
        @elseif ($role === 'candidat')
            <div class="jp-hero">
                <h1>Espace candidat</h1>
                <p>Parcourez les offres d'emploi et postulez en un clic.</p>
            </div>

            <div class="jp-grid">
                <a href="{{ route('offer.index') }}" class="jp-card">
                    <span class="jp-card-tag">Emploi</span>
                    <h2>Voir les offres</h2>
                    <p>Consulter les offres et postuler.</p>
                </a>
                <a href="{{ route('candidat.index') }}" class="jp-card">
                    <span class="jp-card-tag">Communauté</span>
                    <h2>Candidats</h2>
                    <p>Voir la liste des candidats.</p>
                </a>
            </div>
        @endif
    @else
        {{-- ======================================== --}}
        {{-- VISITEUR NON CONNECTÉ --}}
        {{-- ======================================== --}}
        <div class="jp-hero">
            <h1>Connectez-vous</h1>
            <p>Le tableau de bord est réservé aux utilisateurs connectés.</p>
            <a href="{{ route('login') }}" class="jp-btn">Se connecter</a>
        </div>
    @endauth

</x-layouts::jobpilot>
