<?php

use Livewire\Component;

new class extends Component {}; ?>

{{-- BLOC « SUPPRIMER LE COMPTE » (affiché dans Paramètres > Profil). --}}
{{-- Le bouton ouvre la fenêtre de confirmation (delete-user-modal). --}}

<section class="mt-10 space-y-6">
    <div class="relative mb-5">
        <flux:heading>{{ __('Delete account') }}</flux:heading>
        <flux:subheading>{{ __('Delete your account and all of its resources') }}</flux:subheading>
    </div>

    {{-- Bouton qui ouvre la fenêtre nommée confirm-user-deletion --}}
    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" data-test="delete-user-button">
            {{ __('Delete account') }}
        </flux:button>
    </flux:modal.trigger>

    {{-- La fenêtre de confirmation elle-même --}}
    <livewire:pages::settings.delete-user-modal />
</section>
