{{-- Layout des pages métier (offres, entreprises, candidats, candidatures) --}}
@props([
    'title' => null,
    'heading' => null,
])

@php
    $role = auth()->user()?->role;
    $spaces = [
        'admin' => 'Administration',
        'company' => 'Espace entreprise',
        'candidat' => 'Espace candidat',
    ];
@endphp

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' - ' : '' }}JobPilot</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/jobpilot.css') }}">
</head>
<body class="jp role-{{ $role ?? 'guest' }}">

    <header class="jp-header">
        <div class="jp-container jp-header-inner">
            <a href="{{ route('home') }}" class="jp-brand">JobPilot</a>

            @auth
                <span class="jp-space">{{ $spaces[$role] ?? '' }}</span>

                {{-- ======================================== --}}
                {{-- MENU SELON LE RÔLE --}}
                {{-- ======================================== --}}
                <nav class="jp-nav">
                    @if (in_array($role, ['admin', 'candidat']))
                        <a href="{{ route('candidat.index') }}" @class(['active' => request()->routeIs('candidat.*')])>Candidats</a>
                    @endif

                    @if ($role === 'admin')
                        <a href="{{ route('company.index') }}" @class(['active' => request()->routeIs('company.*')])>Entreprises</a>
                    @endif

                    <a href="{{ route('offer.index') }}" @class(['active' => request()->routeIs('offer.*')])>Offres</a>

                    @if ($role === 'company')
                        <a href="{{ route('jobposting.index') }}" @class(['active' => request()->routeIs('jobposting.*')])>Candidatures</a>
                    @endif
                </nav>

                <div class="jp-user">
                    <a href="{{ route('profile.edit') }}" class="jp-user-name">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="jp-logout">Déconnexion</button>
                    </form>
                </div>
            @else
                {{-- ======================================== --}}
                {{-- VISITEUR NON CONNECTÉ --}}
                {{-- ======================================== --}}
                <div class="jp-user">
                    <a href="{{ route('login') }}" class="jp-btn jp-btn-ghost">Connexion</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="jp-btn">Créer un compte</a>
                    @endif
                </div>
            @endauth
        </div>
    </header>

    <main class="jp-container jp-main">
        @if ($heading)
            <h1>{{ $heading }}</h1>
        @endif

        {{ $slot }}
    </main>

    <footer class="jp-footer">
        <div class="jp-container">JobPilot · La mise en relation entre entreprises et candidats</div>
    </footer>

</body>
</html>
