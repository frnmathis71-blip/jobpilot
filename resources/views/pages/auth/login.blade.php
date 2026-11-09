{{-- PAGE DE CONNEXION (route /login, gérée par Fortify). --}}
{{-- Le formulaire est envoyé en POST vers login.store ; Fortify vérifie l'e-mail --}}
{{-- et le mot de passe puis redirige vers /dashboard. --}}

<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        {{-- Titre et sous-titre de la page --}}
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        {{-- @chisel-passkeys --}}
        {{-- Bouton de connexion par passkey (si le navigateur le permet) --}}
        <x-passkey-verify />
        {{-- @end-chisel-passkeys --}}

        {{-- Formulaire de connexion classique --}}
        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                {{-- Lien « Mot de passe oublié ? » --}}
                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            {{-- « Se souvenir de moi » : garde la session ouverte plus longtemps --}}
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        {{-- @chisel-registration --}}
        {{-- Lien vers la page d'inscription --}}
        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
        </div>
        {{-- @end-chisel-registration --}}
    </div>
</x-layouts::auth>
