{{-- LAYOUT AUTH « SIMPLE » : logo centré au-dessus du formulaire. --}}
{{-- Squelette HTML complet (<html>, <head>, <body>) des pages de connexion. --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        {{-- Balises <head> communes : titre, favicon, polices, CSS et JS via Vite --}}
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="bg-background flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-2">
                {{-- Logo cliquable qui ramène à la page d'accueil --}}
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium" wire:navigate>
                    <span class="flex h-9 w-9 mb-1 items-center justify-center rounded-md">
                        <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                    </span>
                    <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div class="flex flex-col gap-6">
                    {{-- Contenu de la page (le formulaire de connexion, d'inscription...) --}}
                    {{ $slot }}
                </div>
            </div>
        </div>

        {{-- Zone des notifications « toast », conservée entre les navigations --}}
        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        {{-- Scripts JavaScript nécessaires aux composants Flux --}}
        @fluxScripts
    </body>
</html>
