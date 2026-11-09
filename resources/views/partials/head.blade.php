{{-- CONTENU DU <head>, inclus par tous les layouts avec @include('partials.head'). --}}

{{-- Encodage des caractères et adaptation aux mobiles --}}
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

{{-- Titre de l'onglet : « Titre de la page - Nom de l'app », ou juste le nom de l'app --}}
<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

{{-- Icônes du site (onglet du navigateur, écran d'accueil iPhone) --}}
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

{{-- Polices déclarées dans vite.config.js (Instrument Sans) --}}
@fonts

{{-- CSS (Tailwind + Flux) et JavaScript compilés par Vite. --}}
{{-- Sans « npm run dev » ou « npm run build », le site s'affiche sans style. --}}
@vite(['resources/css/app.css', 'resources/js/app.js'])
{{-- Applique le thème clair / sombre choisi par l'utilisateur --}}
@fluxAppearance
