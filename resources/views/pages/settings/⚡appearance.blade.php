<?php

use Livewire\Component;
use Livewire\Attributes\Title;

// Composant vide : #[Title] définit seulement le titre de l'onglet
new #[Title('Appearance settings')] class extends Component {
    //
}; ?>

{{-- PAGE PARAMÈTRES > APPARENCE (composant Livewire). --}}
{{-- Choix du thème clair, sombre ou celui du système. Tout se passe dans le --}}
{{-- navigateur (x-model="$flux.appearance"), sans appel au serveur. --}}

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Appearance settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>
    </x-pages::settings.layout>
</section>
