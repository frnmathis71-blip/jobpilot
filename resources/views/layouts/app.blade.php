{{-- LAYOUT PRINCIPAL DE L'APPLICATION (utilisateur connecté) --}}
{{-- Utilisation : <x-layouts::app :title="..."> ... </x-layouts::app> --}}
{{-- Il réutilise le layout avec barre latérale (layouts/app/sidebar) --}}
{{-- et place le contenu de la page ($slot) dans la zone principale. --}}

<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
