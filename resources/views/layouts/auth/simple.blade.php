<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f5f6f8] text-zinc-800 antialiased">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-6">
                {{-- Logo JobPilot : retour à l'accueil --}}
                <a href="{{ route('home') }}" class="text-center text-2xl font-bold tracking-tight text-zinc-900">
                    JobPilot
                </a>

                {{-- Carte contenant le formulaire --}}
                <div class="rounded-lg border border-zinc-200 bg-white px-8 py-8 shadow-xs">
                    {{ $slot }}
                </div>

                <p class="text-center text-sm text-zinc-500">
                    La mise en relation entre entreprises et candidats
                </p>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
