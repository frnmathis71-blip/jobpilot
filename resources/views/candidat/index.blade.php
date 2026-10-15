{{-- ======================================== --}}
{{-- UTILISATEUR CONNECTÉ --}}
{{-- ======================================== --}}

@auth

    {{-- ======================================== --}}
    {{-- PARTIE ADMINISTRATEUR --}}
    {{-- ======================================== --}}

    @if(auth()->user()->role === 'admin')
        <p>Bienvenue Administrateur : {{auth()->user()->name}}</p>

        {{-- Liste des candidats --}}
        @foreach($candidats as $candidat)

            <section>
                    {{$candidat->fullname}} <br>

                    {{-- Suppression d'un candidat --}}
                    <form action="{{ route('candidat.destroy', $candidat) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            onclick="return confirm('Confirmer la suppression ?')">
                            Supprimer
                        </button>
                    </form>

                    {{-- Consultation et modification --}}
                    <a href="{{ route('candidat.show', $candidat)}}">Voir</a>
                    <a href="{{ route('candidat.edit', $candidat)}}">Modifier</a>

            </section>

        @endforeach

        {{-- Ajout d'un candidat --}}
        <a href="{{ route('candidat.create', $candidat)}}">Ajouter un candidat</a>

        {{-- Style CSS administrateur --}}
        <style>
            section{
                border:1px solid black;
                margin:5px;
                padding:5px;
            }
        </style>


    {{-- ======================================== --}}
    {{-- PARTIE CANDIDAT --}}
    {{-- ======================================== --}}

    @elseif(auth()->user()->role === 'candidat')
        <p>Bienvenue candidat : {{auth()->user()->name}}</p>

        {{-- Liste des candidats --}}
        @foreach($candidats as $candidat)
            <section>
                {{$candidat->fullname}} <br>
            </section>
        @endforeach

        {{-- Style CSS candidat --}}
        <style>
            section{
                border:1px solid black;
                margin:5px;
                padding:5px;
            }
        </style>


    {{-- ======================================== --}}
    {{-- PARTIE ENTREPRISE --}}
    {{-- ======================================== --}}

    @elseif(auth()->user()->role === 'company')
        <p>Bienvenue entreprise</p>

    @endif

@endauth