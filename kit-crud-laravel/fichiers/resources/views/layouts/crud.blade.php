<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('titre')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        nav a { margin-right: 15px; }
        table { border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; }
        .succes { color: green; }
        .erreur { color: red; margin: 2px 0; }
        form div { margin-bottom: 10px; }
    </style>
</head>
<body>
    <nav>
        <a href="{{ route('PARENT_PLURIEL.index') }}">PARENT_PLURIEL</a>
        <a href="{{ route('PLURIEL.index') }}">PLURIEL</a>
    </nav>

    @if (session('success'))
        <p class="succes">{{ session('success') }}</p>
    @endif

    @yield('content')
</body>
</html>
