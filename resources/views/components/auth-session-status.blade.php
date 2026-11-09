{{-- COMPOSANT <x-auth-session-status> : message de confirmation en vert --}}
{{-- (ex. « lien de réinitialisation envoyé »). N'affiche rien si $status est vide. --}}

{{-- Paramètre attendu : le message à afficher --}}
@props([
    'status',
])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
