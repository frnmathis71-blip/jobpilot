{{-- PAGE DE VÉRIFICATION DE L'E-MAIL. --}}
{{-- Affichée tant que l'utilisateur n'a pas cliqué sur le lien reçu par e-mail. --}}

<x-layouts::auth :title="__('Email verification')">
    <div class="mt-4 flex flex-col gap-6">
        <flux:text class="text-center">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </flux:text>

        {{-- Message affiché après un nouvel envoi du lien --}}
        @if (session('status') == 'verification-link-sent')
            <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </flux:text>
        @endif

        <div class="flex flex-col items-center justify-between space-y-3">
            {{-- Bouton pour renvoyer l'e-mail de vérification --}}
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <flux:button type="submit" variant="primary" class="w-full">
                    {{ __('Resend verification email') }}
                </flux:button>
            </form>

            {{-- Bouton de déconnexion --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <flux:button variant="ghost" type="submit" class="text-sm cursor-pointer" data-test="logout-button">
                    {{ __('Log out') }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts::auth>
