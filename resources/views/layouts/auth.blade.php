{{-- LAYOUT DES PAGES D'AUTHENTIFICATION (connexion, inscription...) --}}
{{-- Utilisation : <x-layouts::auth :title="..."> ... </x-layouts::auth> --}}
{{-- Il réutilise la variante « simple » (layouts/auth/simple). --}}
{{-- Pour changer de style, remplacer auth.simple par auth.card ou auth.split. --}}

<x-layouts::auth.simple :title="$title ?? null">
    {{ $slot }}
</x-layouts::auth.simple>
