<x-layouts::jobpilot title="Accueil">

    {{-- ======================================== --}}
    {{-- VISITEUR NON CONNECTÉ --}}
    {{-- ======================================== --}}
    @guest
        <div class="jp-hero">
            <h1>Le lien entre les entreprises et les candidats</h1>
            <p>JobPilot permet aux entreprises de publier leurs offres d'emploi et aux candidats d'y postuler en quelques clics.</p>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="jp-btn">Créer un compte</a>
            @endif
            <a href="{{ route('login') }}" class="jp-btn jp-btn-ghost">Se connecter</a>
        </div>

        <div class="jp-grid">
            <div class="jp-card is-candidat">
                <span class="jp-card-tag">Vous êtes candidat</span>
                <h2>Trouvez votre prochain poste</h2>
                <p>Consultez les offres publiées et postulez directement en ligne.</p>
            </div>

            <div class="jp-card is-company">
                <span class="jp-card-tag">Vous êtes une entreprise</span>
                <h2>Recrutez les bons profils</h2>
                <p>Publiez vos offres et suivez les candidatures reçues au même endroit.</p>
            </div>
        </div>
    @endguest

    @auth
        <div class="jp-hero">
            <h1>Bonjour {{ auth()->user()->name }}</h1>
            <p>Accédez rapidement aux rubriques de votre espace.</p>
        </div>

        <div class="jp-grid">
            {{-- ======================================== --}}
            {{-- ADMINISTRATEUR ET CANDIDAT : lien Candidats --}}
            {{-- ======================================== --}}
            @if (in_array(auth()->user()->role, ['admin', 'candidat']))
                <a href="{{ route('candidat.index') }}" class="jp-card">
                    <h2>Candidats</h2>
                    <p>Voir la liste des candidats.</p>
                </a>
            @endif

            {{-- ======================================== --}}
            {{-- ADMINISTRATEUR : lien Entreprises --}}
            {{-- ======================================== --}}
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('company.index') }}" class="jp-card">
                    <h2>Entreprises</h2>
                    <p>Gérer les entreprises inscrites.</p>
                </a>
            @endif

            {{-- ======================================== --}}
            {{-- TOUS LES RÔLES : lien Offres --}}
            {{-- ======================================== --}}
            @if (in_array(auth()->user()->role, ['admin', 'company', 'candidat']))
                <a href="{{ route('offer.index') }}" class="jp-card">
                    <h2>Offres</h2>
                    <p>Consulter les offres d'emploi.</p>
                </a>
            @endif

            {{-- ======================================== --}}
            {{-- ENTREPRISE : lien Candidatures --}}
            {{-- ======================================== --}}
            @if (auth()->user()->role === 'company')
                <a href="{{ route('jobposting.index') }}" class="jp-card">
                    <h2>Candidatures</h2>
                    <p>Voir qui a postulé à vos offres.</p>
                </a>
            @endif
        </div>
    @endauth

</x-layouts::jobpilot>
