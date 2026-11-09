{{-- COMPOSANT <x-auth-header> : titre + sous-titre des pages d'authentification. --}}
{{-- Exemple : <x-auth-header :title="__('Log in')" :description="..." /> --}}

{{-- Paramètres attendus par le composant --}}
@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl" level="1">{{ $title }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
