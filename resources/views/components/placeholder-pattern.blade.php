{{-- COMPOSANT <x-placeholder-pattern> : motif de hachures en SVG --}}
{{-- utilisé comme fond provisoire (ex. cases vides du tableau de bord). --}}
{{-- Un id unique (uniqid) évite les conflits si le motif apparaît plusieurs fois. --}}

@props([
    'id' => uniqid(),
])

<svg {{ $attributes }} fill="none">
    <defs>
        <pattern id="pattern-{{ $id }}" x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
            <path d="M-1 5L5 -1M3 9L8.5 3.5" stroke-width="0.5"></path>
        </pattern>
    </defs>
    <rect stroke="none" fill="url(#pattern-{{ $id }})" width="100%" height="100%"></rect>
</svg>
