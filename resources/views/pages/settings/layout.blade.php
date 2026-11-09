{{-- LAYOUT DES PAGES DE PARAMÈTRES : menu à gauche, contenu à droite. --}}
{{-- Utilisation : <x-pages::settings.layout :heading="..." :subheading="..."> --}}

<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        {{-- Menu : Profil, Sécurité, Apparence --}}
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    {{-- Séparateur visible seulement sur mobile --}}
    <flux:separator class="md:hidden" />

    {{-- Titre, sous-titre et contenu de la page --}}
    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
