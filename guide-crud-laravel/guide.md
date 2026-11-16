---
title: "Guide pas à pas : CRUD Laravel complet"
subtitle: "De la création du projet jusqu'à l'authentification, les rôles, les Gates et les Policies"
lang: fr
---

## Étape 0 : Remplacer les mots en MAJUSCULES

Dans VS Code, ouvre Rechercher/Remplacer (`Ctrl+H`) et **active « Aa »** pour respecter la casse.

| Mot à remplacer | Par quoi | Exemple |
|---|---|---|
| `NOM_PROJET` | nom du projet | `boutique` |
| `MODELE` | modèle, singulier, majuscule au début | `Produit` |
| `PLURIEL` | pluriel, en minuscules (nom de la table, des routes et du dossier des vues) | `produits` |
| `SINGULIER` | singulier, en minuscules | `produit` |
| `CHAMP1` | 1re colonne (texte) | `nom` |
| `CHAMP2` | 2e colonne (nombre) | `prix` |

> ⚠️ Utilise **exactement le même nom** de colonne partout : migration, `name="..."` du formulaire, validation et `$fillable`. Par exemple, `adress` et `address` sont deux noms différents pour Laravel.

## Étape 1 : Créer le projet et configurer la base de données

**Commandes**

```bash
composer create-project laravel/laravel NOM_PROJET
```

```bash
cd NOM_PROJET
```

**Fichier :** `.env` (vérifie ou modifie cette ligne)

```env
DB_CONNECTION=sqlite
```

**Commande** (dans un 1er terminal, à laisser ouvert ; les autres commandes se tapent dans un 2e terminal)

```bash
php artisan serve
```

> ✅ **Vérification :** ouvre http://127.0.0.1:8000. Tu dois voir la page d'accueil Laravel.

> ⚠️ **Si on t'impose MySQL**, mets ceci dans `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=NOM_BASE
DB_USERNAME=root
DB_PASSWORD=
```

> ⚠️ **Si Laravel ignore tes changements du `.env`**, tape : `php artisan optimize:clear`

## Étape 2 : Créer le modèle, la migration, la factory et le seeder

**Commande**

```bash
php artisan make:model MODELE -mfs
```

### 2a. La migration

**Fichier :** `database/migrations/xxxx_xx_xx_xxxxxx_create_PLURIEL_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PLURIEL', function (Blueprint $table) {
            $table->id();
            $table->string('CHAMP1');
            $table->decimal('CHAMP2', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PLURIEL');
    }
};
```

Autres types de colonnes (une ligne par colonne) :

| Type | Migration | Champ du formulaire | Validation | Factory |
|---|---|---|---|---|
| Texte court | `$table->string('x');` | `type="text"` | `required\|string\|max:255` | `fake()->word()` |
| Texte long | `$table->text('x');` | `<textarea>` | `required\|string` | `fake()->paragraph()` |
| Entier | `$table->integer('x');` | `type="number"` | `required\|integer` | `fake()->numberBetween(1, 100)` |
| Décimal | `$table->decimal('x', 8, 2);` | `type="number" step="0.01"` | `required\|numeric` | `fake()->randomFloat(2, 1, 100)` |
| Booléen | `$table->boolean('x');` | `type="checkbox"` | `boolean` | `fake()->boolean()` |
| Date | `$table->date('x');` | `type="date"` | `required\|date` | `fake()->date()` |
| E-mail | `$table->string('x');` | `type="email"` | `required\|email` | `fake()->email()` |
| Facultatif | `->nullable()` en fin de ligne | – | `nullable` au lieu de `required` | – |

### 2b. Le modèle

**Fichier :** `app/Models/MODELE.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MODELE extends Model
{
    use HasFactory;

    protected $fillable = [
        'CHAMP1',
        'CHAMP2',
    ];
}
```

> ⚠️ **Si tu as `Add [xxx] to fillable property`**, ajoute `'xxx'` dans `$fillable`.
>
> ⚠️ **Si tu as `no such table` alors que la migration est lancée** (pluriel irrégulier, par ex. `animaux`), ajoute `protected $table = 'PLURIEL';` dans le modèle.

### 2c. La factory

**Fichier :** `database/factories/MODELEFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MODELEFactory extends Factory
{
    public function definition(): array
    {
        return [
            'CHAMP1' => fake()->word(),
            'CHAMP2' => fake()->randomFloat(2, 1, 100),
        ];
    }
}
```

### 2d. Le seeder

**Fichier :** `database/seeders/MODELESeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MODELE;

class MODELESeeder extends Seeder
{
    public function run(): void
    {
        MODELE::factory()->count(20)->create();
    }
}
```

### 2e. Le seeder principal

**Fichier :** `database/seeders/DatabaseSeeder.php` (si le fichier contient déjà du code, garde-le et ajoute seulement le bloc `$this->call` à la fin de `run()`)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            MODELESeeder::class,
        ]);
    }
}
```

> ✅ **Vérification :** les 4 fichiers existent et ne sont pas soulignés en rouge.

## Étape 3 : Lancer les migrations et remplir la base

**Commandes**

```bash
php artisan migrate
```

```bash
php artisan db:seed
```

> ✅ **Vérification :** lance `php artisan tinker`, puis tape `MODELE::count();`. Le résultat doit être `20`. Tape `exit` pour sortir.

> ⚠️ **Si tu as `Call to undefined method ...::factory()`**, il manque `use HasFactory;` dans le modèle (étape 2b).
>
> ⚠️ **Si tu as `Unknown format`**, tu as fait une faute de frappe dans `fake()->...` (par ex. `firtname` au lieu de `firstName`).
>
> ⚠️ **Si tu dois tout recommencer** (cette commande efface toutes les données) : `php artisan migrate:fresh --seed`

## Étape 4 : Créer le contrôleur

**Commande**

```bash
php artisan make:controller MODELEController --resource --model=MODELE
```

> ✅ **Vérification :** le fichier `app/Http/Controllers/MODELEController.php` existe et contient 7 méthodes vides.

## Étape 5 : Déclarer les routes

**Fichier :** `routes/web.php` (s'il contient déjà d'autres routes ou un `require ...`, garde-les et ajoute seulement le `use` et `Route::resource`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MODELEController;

Route::get('/', function () {
    return redirect()->route('PLURIEL.index');
});

Route::resource('PLURIEL', MODELEController::class);
```

**Commande**

```bash
php artisan route:list --path=PLURIEL
```

> ✅ **Vérification :** tu dois voir 7 routes (`PLURIEL.index`, `.create`, `.store`, `.show`, `.edit`, `.update`, `.destroy`), et l'URI doit contenir `PLURIEL/{SINGULIER}`.

> ⚠️ **Si le nom entre `{ }` n'est pas `SINGULIER`** (par ex. `categories` donne `{category}`), Laravel ne retrouvera pas l'élément et tu obtiendras un modèle vide. Remplace la ligne par :

```php
Route::resource('PLURIEL', MODELEController::class)->parameters(['PLURIEL' => 'SINGULIER']);
```

> ⚠️ **Si tu as `Target class [MODELEController] does not exist`**, il manque le `use App\Http\Controllers\MODELEController;`.
>
> ⚠️ **Si une route ajoutée ne s'affiche pas**, tape : `php artisan route:clear`

## Étape 6 : Créer les vues Blade

Crée le dossier `resources/views/layouts/` et le dossier `resources/views/PLURIEL/`.

### 6a. Le layout

**Fichier :** `resources/views/layouts/app.blade.php`

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>@yield('titre')</title>
</head>
<body>
    <nav>
        <a href="{{ route('PLURIEL.index') }}">Liste</a>
    </nav>

    @if (session('success'))
        <p style="color: green">{{ session('success') }}</p>
    @endif

    @yield('content')
</body>
</html>
```

### 6b. Le formulaire partagé (création et modification)

**Fichier :** `resources/views/PLURIEL/_form.blade.php`

```html
@csrf

<div>
    <label for="CHAMP1">CHAMP1</label>
    <input type="text" id="CHAMP1" name="CHAMP1"
           value="{{ old('CHAMP1', $SINGULIER->CHAMP1 ?? '') }}">
    @error('CHAMP1')
        <p style="color: red">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="CHAMP2">CHAMP2</label>
    <input type="number" step="0.01" id="CHAMP2" name="CHAMP2"
           value="{{ old('CHAMP2', $SINGULIER->CHAMP2 ?? '') }}">
    @error('CHAMP2')
        <p style="color: red">{{ $message }}</p>
    @enderror
</div>
```

### 6c. La liste

**Fichier :** `resources/views/PLURIEL/index.blade.php`

```html
@extends('layouts.app')

@section('titre', 'Liste')

@section('content')
    <h1>Liste des PLURIEL</h1>

    <a href="{{ route('PLURIEL.create') }}">Ajouter</a>

    <table border="1">
        <tr>
            <th>CHAMP1</th>
            <th>CHAMP2</th>
            <th>Actions</th>
        </tr>
        @forelse ($PLURIEL as $SINGULIER)
            <tr>
                <td>{{ $SINGULIER->CHAMP1 }}</td>
                <td>{{ $SINGULIER->CHAMP2 }}</td>
                <td>
                    <a href="{{ route('PLURIEL.show', $SINGULIER) }}">Voir</a>
                    <a href="{{ route('PLURIEL.edit', $SINGULIER) }}">Modifier</a>
                    <form action="{{ route('PLURIEL.destroy', $SINGULIER) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3">Aucun élément.</td>
            </tr>
        @endforelse
    </table>
@endsection
```

### 6d. La création

**Fichier :** `resources/views/PLURIEL/create.blade.php`

```html
@extends('layouts.app')

@section('titre', 'Ajouter')

@section('content')
    <h1>Ajouter</h1>

    <form action="{{ route('PLURIEL.store') }}" method="POST">
        @include('PLURIEL._form')
        <button type="submit">Enregistrer</button>
    </form>
@endsection
```

### 6e. La modification

**Fichier :** `resources/views/PLURIEL/edit.blade.php`

```html
@extends('layouts.app')

@section('titre', 'Modifier')

@section('content')
    <h1>Modifier « {{ $SINGULIER->CHAMP1 }} »</h1>

    <form action="{{ route('PLURIEL.update', $SINGULIER) }}" method="POST">
        @method('PUT')
        @include('PLURIEL._form')
        <button type="submit">Modifier</button>
    </form>
@endsection
```

### 6f. Le détail

**Fichier :** `resources/views/PLURIEL/show.blade.php`

```html
@extends('layouts.app')

@section('titre', 'Détail')

@section('content')
    <h1>{{ $SINGULIER->CHAMP1 }}</h1>

    <p>CHAMP2 : {{ $SINGULIER->CHAMP2 }}</p>

    <a href="{{ route('PLURIEL.edit', $SINGULIER) }}">Modifier</a>

    <form action="{{ route('PLURIEL.destroy', $SINGULIER) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
    </form>

    <a href="{{ route('PLURIEL.index') }}">Retour à la liste</a>
@endsection
```

> ✅ **Vérification :** le dossier `resources/views/PLURIEL/` contient 5 fichiers (`_form`, `index`, `create`, `edit`, `show`), tous en `.blade.php`. Tu ne peux tester dans le navigateur qu'après l'étape 7.

> ⚠️ **Si tu as `View [PLURIEL.index] not found`**, vérifie le nom du dossier ou du fichier. Il faut bien l'extension `.blade.php`.

## Étape 7 : Écrire les 7 méthodes du contrôleur

**Fichier :** `app/Http/Controllers/MODELEController.php` (remplace tout le contenu)

```php
<?php

namespace App\Http\Controllers;

use App\Models\MODELE;
use Illuminate\Http\Request;

class MODELEController extends Controller
{
    public function index()
    {
        $PLURIEL = MODELE::all();

        return view('PLURIEL.index', compact('PLURIEL'));
    }

    public function create()
    {
        return view('PLURIEL.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
        ]);

        MODELE::create($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Ajouté avec succès.');
    }

    public function show(MODELE $SINGULIER)
    {
        return view('PLURIEL.show', compact('SINGULIER'));
    }

    public function edit(MODELE $SINGULIER)
    {
        return view('PLURIEL.edit', compact('SINGULIER'));
    }

    public function update(Request $request, MODELE $SINGULIER)
    {
        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
        ]);

        $SINGULIER->update($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Modifié avec succès.');
    }

    public function destroy(MODELE $SINGULIER)
    {
        $SINGULIER->delete();

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Supprimé avec succès.');
    }
}
```

> ✅ **Vérification** (teste dans cet ordre) :
>
> 1. Va sur `/PLURIEL`. Tu dois voir les 20 éléments.
> 2. Clique sur « Ajouter », remplis le formulaire et enregistre. Tu reviens sur la liste avec le message vert, et le nouvel élément apparaît.
> 3. Clique sur « Voir ». Le détail s'affiche.
> 4. Clique sur « Modifier », change une valeur et enregistre. La valeur est mise à jour dans la liste.
> 5. Clique sur « Supprimer » et confirme. L'élément disparaît.
> 6. Va sur `/PLURIEL/99999`. Tu dois avoir une erreur 404, c'est le comportement normal.

⚠️ **Selon l'erreur que tu obtiens :**

| Erreur | Solution |
|---|---|
| `419 Page Expired` | Il manque `@csrf` dans le formulaire. |
| `405 Method Not Allowed` / `Method ... not supported` | Il manque `@method('PUT')` ou `@method('DELETE')`. |
| `Undefined variable $PLURIEL` | Le nom dans `compact('...')` doit être identique à celui de la vue. |
| `Class "App\Models\MODELE" not found` | Il manque `use App\Models\MODELE;` en haut du contrôleur. |
| `Route [xxx] not defined` | Recopie le nom exact depuis `php artisan route:list`. |
| `Missing required parameter` | Écris `route('PLURIEL.show', $SINGULIER)` avec le 2e argument. |
| Le formulaire d'édition est vide | Le nom de la variable `MODELE $SINGULIER` doit être identique au `{SINGULIER}` de la route (voir étape 5). |
| `Base table or view not found` / `no such column` | Lance `php artisan migrate` et vérifie le nom des colonnes. |
| Page 500 sans détail | Mets `APP_DEBUG=true` dans `.env` et lis `storage/logs/laravel.log`. |

## Étape 8 : Vérifier et adapter la validation

Les règles sont déjà dans `store()` et `update()`, et l'affichage des erreurs est déjà dans `_form` (`@error` + `old()`).

**Fichier :** `app/Http/Controllers/MODELEController.php`. Adapte le tableau `validate([...])` **dans `store()` ET dans `update()`** (une ligne par champ du formulaire). Règles disponibles :

| Règle | Effet |
|---|---|
| `required` / `nullable` | Champ obligatoire / champ qui peut rester vide |
| `string` / `integer` / `numeric` / `boolean` | Type attendu |
| `max:255` / `min:3` | Longueur maximale / minimale (ou valeur pour un nombre) |
| `between:1,100` | Valeur comprise entre deux bornes |
| `email` / `date` / `url` | Format attendu |
| `unique:PLURIEL,CHAMP1` | Pas de doublon dans la colonne |
| `exists:autre_table,id` | La valeur doit exister dans une autre table |
| `in:valeur1,valeur2` | Valeur choisie dans une liste |

> ✅ **Vérification :** envoie le formulaire de création vide. Tu restes sur la page, avec les messages d'erreur en rouge sous chaque champ, et les valeurs déjà saisies sont conservées.

> ⚠️ **Si tu as `Add [xxx] to fillable`**, tout champ que tu valides doit aussi être dans `$fillable` (étape 2b).
>
> ⚠️ **Si tu as `Undefined variable $request`**, tu as mis la validation dans `edit()`. Elle doit aller dans `update()`.

## Étape 9 : Ajouter les relations entre modèles (si besoin)

Suis la **recette « Ajouter une relation »** à la fin de ce guide.

## Étape 10 : Authentification, rôles, middleware, Gate et Policy

> ⚠️ **Prérequis :** le projet doit déjà avoir la connexion (Fortify ou starter kit, comme JobPilot). Si tu as `Route [login] not defined`, c'est que le projet n'a pas de système de connexion.

### 10.1 Ajouter la colonne `role` aux utilisateurs

**Fichier :** `database/migrations/0001_01_01_000000_create_users_table.php`, dans `Schema::create('users', ...)`, sous `$table->string('password');` :

```php
$table->enum('role', ['candidat', 'company', 'admin'])->default('candidat');
```

**Commande**

```bash
php artisan migrate:fresh --seed
```

> ✅ **Vérification :** `php artisan migrate:status` affiche toutes les migrations en « Ran ».

### 10.2 Donner un rôle à un utilisateur (l'admin se met à la main)

**Commande**

```bash
php artisan tinker
```

Puis dans Tinker :

```php
User::where('email', 'EMAIL_UTILISATEUR')->update(['role' => 'admin']);
```

> ✅ **Vérification :** `User::where('email', 'EMAIL_UTILISATEUR')->first()->role;` affiche `"admin"`.

### 10.3 Protéger toutes les routes par la connexion

Si tu veux seulement obliger à être connecté, sans rôle, cette version suffit. Sinon, passe à 10.4.

**Fichier :** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MODELEController;

Route::middleware(['auth'])->group(function () {
    Route::resource('PLURIEL', MODELEController::class);
});
```

> ✅ **Vérification :** déconnecté, tu vas sur `/PLURIEL` et tu es redirigé vers `/login`.

### 10.4 Créer un middleware de rôle

**Commande** (`NOM_MIDDLEWARE` par ex. `IsCompany`, `ROLE` par ex. `company`)

```bash
php artisan make:middleware NOM_MIDDLEWARE
```

**Fichier :** `app/Http/Middleware/NOM_MIDDLEWARE.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NOM_MIDDLEWARE
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->role !== 'ROLE') {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}
```

### 10.5 Déclarer l'alias du middleware

**Fichier :** `bootstrap/app.php`. Ajoute le `use` en haut, et ce contenu dans `->withMiddleware(...)`. Ne touche pas au reste du fichier.

```php
use App\Http\Middleware\NOM_MIDDLEWARE;
```

```php
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'NOM_MIDDLEWARE' => NOM_MIDDLEWARE::class,
        ]);
    })
```

### 10.6 Appliquer le rôle sur les routes

**Fichier :** `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MODELEController;

// 1) Réservé au rôle : créer, modifier, supprimer
Route::middleware(['auth', 'NOM_MIDDLEWARE'])->group(function () {
    Route::resource('PLURIEL', MODELEController::class)
        ->except(['index', 'show']);
});

// 2) Tout utilisateur connecté : consulter
Route::middleware(['auth'])->group(function () {
    Route::resource('PLURIEL', MODELEController::class)
        ->only(['index', 'show']);
});
```

> ⚠️ **Garde cet ordre (groupe avec `create` d'abord).** Sinon, `/PLURIEL/create` est lu comme `/PLURIEL/{SINGULIER}` et renvoie une erreur 404.

> ✅ **Vérification :**
>
> - Connecté avec le bon rôle, `/PLURIEL/create` fonctionne.
> - Connecté avec un autre rôle, `/PLURIEL/create` renvoie une erreur 403.
> - Déconnecté, tu es redirigé vers `/login`.

> ⚠️ **Si tu as `Target class [NOM_MIDDLEWARE] does not exist`**, l'alias ou le `use` manque dans `bootstrap/app.php`.

### 10.7 Créer une Gate (règle sans modèle, par ex. l'accès admin)

**Fichier :** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('NOM_GATE', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
```

Pour l'utiliser dans une méthode du contrôleur (ajoute `use Illuminate\Support\Facades\Gate;` en haut) :

```php
Gate::authorize('NOM_GATE');
```

Pour l'utiliser dans une vue, par exemple dans le `<nav>` du layout :

```html
@can('NOM_GATE')
    <a href="{{ route('PLURIEL.index') }}">Espace admin</a>
@endcan
```

> ✅ **Vérification :** le lien n'apparaît que pour un admin, et la méthode protégée renvoie une erreur 403 aux autres.

### 10.8 Créer une Policy (« c'est TON élément »)

Utilise-la quand une entreprise ne doit modifier que ses propres éléments.

**Commande**

```bash
php artisan make:policy MODELEPolicy --model=MODELE
```

**Fichier :** `app/Policies/MODELEPolicy.php` (`CLE_PROPRIO` par ex. `company_id`)

```php
<?php

namespace App\Policies;

use App\Models\MODELE;
use App\Models\User;

class MODELEPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MODELE $SINGULIER): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->role === 'ROLE';
    }

    public function update(User $user, MODELE $SINGULIER): bool
    {
        return $user->role === 'ROLE'
            && $user->CLE_PROPRIO === $SINGULIER->CLE_PROPRIO;
    }

    public function delete(User $user, MODELE $SINGULIER): bool
    {
        return $user->role === 'ROLE'
            && $user->CLE_PROPRIO === $SINGULIER->CLE_PROPRIO;
    }
}
```

Si l'élément appartient directement à l'utilisateur, remplace la comparaison par `$user->id === $SINGULIER->user_id`.

**Fichier :** `app/Http/Controllers/MODELEController.php` (remplace tout le contenu)

```php
<?php

namespace App\Http\Controllers;

use App\Models\MODELE;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MODELEController extends Controller
{
    public function index()
    {
        $PLURIEL = MODELE::all();

        return view('PLURIEL.index', compact('PLURIEL'));
    }

    public function create()
    {
        Gate::authorize('create', MODELE::class);

        return view('PLURIEL.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', MODELE::class);

        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
        ]);

        MODELE::create($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Ajouté avec succès.');
    }

    public function show(MODELE $SINGULIER)
    {
        return view('PLURIEL.show', compact('SINGULIER'));
    }

    public function edit(MODELE $SINGULIER)
    {
        Gate::authorize('update', $SINGULIER);

        return view('PLURIEL.edit', compact('SINGULIER'));
    }

    public function update(Request $request, MODELE $SINGULIER)
    {
        Gate::authorize('update', $SINGULIER);

        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
        ]);

        $SINGULIER->update($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Modifié avec succès.');
    }

    public function destroy(MODELE $SINGULIER)
    {
        Gate::authorize('delete', $SINGULIER);

        $SINGULIER->delete();

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Supprimé avec succès.');
    }
}
```

**Fichier :** `resources/views/PLURIEL/index.blade.php`. Entoure les boutons « Modifier » et « Supprimer » :

```html
@can('update', $SINGULIER)
    <a href="{{ route('PLURIEL.edit', $SINGULIER) }}">Modifier</a>
@endcan
@can('delete', $SINGULIER)
    <form action="{{ route('PLURIEL.destroy', $SINGULIER) }}" method="POST" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
    </form>
@endcan
```

> ⚠️ `@can` ne fait que **cacher** le bouton. La vraie protection, c'est le `Gate::authorize` dans le contrôleur : ne l'enlève jamais.

> ✅ **Vérification :** connecté avec une entreprise A, tape à la main `/PLURIEL/{id d'un élément de l'entreprise B}/edit`. Tu dois avoir une erreur 403. Un admin, lui, passe partout.

## ☐ Checklist finale

- ☐ `.env` configuré, `php artisan serve` lancé
- ☐ Migration remplie (colonnes correctes)
- ☐ `php artisan migrate` lancé
- ☐ `$fillable` contient **tous** les champs du formulaire
- ☐ `use HasFactory;` présent dans le modèle
- ☐ Factory et seeder remplis, `DatabaseSeeder` appelle le seeder
- ☐ `php artisan db:seed` lancé (`MODELE::count()` > 0)
- ☐ Contrôleur créé avec `--resource --model=MODELE`
- ☐ `use ...Controller;` et `Route::resource` dans `web.php`
- ☐ `php artisan route:list --path=PLURIEL` affiche 7 routes, avec `{SINGULIER}`
- ☐ Layout, `_form`, `index`, `create`, `edit` et `show` créés
- ☐ `@csrf` dans chaque formulaire
- ☐ `@method('PUT')` dans `edit`, `@method('DELETE')` dans les suppressions
- ☐ Les 7 méthodes écrites, avec une redirection après `store`, `update` et `destroy`
- ☐ Validation présente dans `store()` **et** `update()`
- ☐ Formulaire vide envoyé : les erreurs rouges s'affichent
- ☐ Création, lecture, modification et suppression testées dans le navigateur
- ☐ (Auth) Routes protégées par `auth`, groupe du rôle placé **avant** `index`/`show`
- ☐ (Rôles) Alias dans `bootstrap/app.php`, test d'une erreur 403 avec un mauvais rôle
- ☐ (Policy) `Gate::authorize` dans le contrôleur, `@can` dans la vue

**Commandes de secours :**

```bash
php artisan optimize:clear
```

```bash
php artisan route:clear
```

```bash
php artisan migrate:fresh --seed
```

## 🍳 Recette : Ajouter une relation (un PARENT a plusieurs MODELE)

Exemple : une `Categorie` a plusieurs `Produit`. Le MODELE déjà fait est l'**enfant**.

**Nouveaux mots à remplacer, à faire EN PREMIER** (sinon `SINGULIER` est remplacé à l'intérieur de `PARENT_SINGULIER`) :

| Mot | Exemple |
|---|---|
| `PARENT_MODELE` | `Categorie` |
| `PARENT_PLURIEL` | `categories` |
| `PARENT_SINGULIER` | `categorie` |

### R1. Créer le parent

```bash
php artisan make:model PARENT_MODELE -mfs
```

### R2. La migration du parent

**Fichier :** `database/migrations/xxxx_create_PARENT_PLURIEL_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('PARENT_PLURIEL', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('PARENT_PLURIEL');
    }
};
```

> ⚠️ **Renomme le fichier** pour que sa date soit **plus ancienne** que celle de la migration de l'enfant. Par exemple, `2026_11_16_090000_create_PARENT_PLURIEL_table.php` doit passer avant `2026_11_16_100000_create_PLURIEL_table.php`. Sinon, la clé étrangère plante.

### R3. Ajouter la clé étrangère chez l'enfant

**Fichier :** `database/migrations/xxxx_create_PLURIEL_table.php`

```php
        Schema::create('PLURIEL', function (Blueprint $table) {
            $table->id();
            $table->string('CHAMP1');
            $table->decimal('CHAMP2', 8, 2);
            $table->foreignId('PARENT_SINGULIER_id')->constrained('PARENT_PLURIEL');
            $table->timestamps();
        });
```

### R4. Les modèles

**Fichier :** `app/Models/PARENT_MODELE.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MODELE;

class PARENT_MODELE extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function PLURIEL()
    {
        return $this->hasMany(MODELE::class);
    }
}
```

**Fichier :** `app/Models/MODELE.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PARENT_MODELE;

class MODELE extends Model
{
    use HasFactory;

    protected $fillable = [
        'CHAMP1',
        'CHAMP2',
        'PARENT_SINGULIER_id',
    ];

    public function PARENT_SINGULIER()
    {
        return $this->belongsTo(PARENT_MODELE::class);
    }
}
```

### R5. Les factories

**Fichier :** `database/factories/PARENT_MODELEFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PARENT_MODELEFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->word(),
        ];
    }
}
```

**Fichier :** `database/factories/MODELEFactory.php`

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PARENT_MODELE;

class MODELEFactory extends Factory
{
    public function definition(): array
    {
        return [
            'CHAMP1' => fake()->word(),
            'CHAMP2' => fake()->randomFloat(2, 1, 100),
            'PARENT_SINGULIER_id' => PARENT_MODELE::inRandomOrder()->first()->id,
        ];
    }
}
```

### R6. Les seeders (le parent d'abord)

**Fichier :** `database/seeders/PARENT_MODELESeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PARENT_MODELE;

class PARENT_MODELESeeder extends Seeder
{
    public function run(): void
    {
        PARENT_MODELE::factory()->count(5)->create();
    }
}
```

**Fichier :** `database/seeders/DatabaseSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PARENT_MODELESeeder::class,
            MODELESeeder::class,
        ]);
    }
}
```

### R7. Recréer la base

```bash
php artisan migrate:fresh --seed
```

> ✅ **Vérification :** dans `php artisan tinker`, `MODELE::first()->PARENT_SINGULIER;` affiche un objet, et `PARENT_MODELE::first()->PLURIEL;` affiche une liste.

> ⚠️ **Si tu as `NOT NULL constraint failed: ..._id`** ou une erreur sur `->id` à null, c'est que le seeder du parent n'est pas appelé **avant** celui de l'enfant.

### R8. Le contrôleur de l'enfant

**Fichier :** `app/Http/Controllers/MODELEController.php`. Ajoute en haut :

```php
use App\Models\PARENT_MODELE;
```

Puis remplace ces 4 méthodes :

```php
    public function create()
    {
        $PARENT_PLURIEL = PARENT_MODELE::all();

        return view('PLURIEL.create', compact('PARENT_PLURIEL'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
            'PARENT_SINGULIER_id' => 'required|exists:PARENT_PLURIEL,id',
        ]);

        MODELE::create($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Ajouté avec succès.');
    }

    public function edit(MODELE $SINGULIER)
    {
        $PARENT_PLURIEL = PARENT_MODELE::all();

        return view('PLURIEL.edit', compact('SINGULIER', 'PARENT_PLURIEL'));
    }

    public function update(Request $request, MODELE $SINGULIER)
    {
        $validated = $request->validate([
            'CHAMP1' => 'required|string|max:255',
            'CHAMP2' => 'required|numeric',
            'PARENT_SINGULIER_id' => 'required|exists:PARENT_PLURIEL,id',
        ]);

        $SINGULIER->update($validated);

        return redirect()
            ->route('PLURIEL.index')
            ->with('success', 'Modifié avec succès.');
    }
```

Si tu as fait l'étape 10.8, garde les lignes `Gate::authorize(...)` en tête de ces méthodes.

### R9. La liste déroulante dans le formulaire

**Fichier :** `resources/views/PLURIEL/_form.blade.php` (à ajouter à la fin)

```html
<div>
    <label for="PARENT_SINGULIER_id">PARENT_MODELE</label>
    <select id="PARENT_SINGULIER_id" name="PARENT_SINGULIER_id">
        @foreach ($PARENT_PLURIEL as $PARENT_SINGULIER)
            <option value="{{ $PARENT_SINGULIER->id }}"
                {{ old('PARENT_SINGULIER_id', $SINGULIER->PARENT_SINGULIER_id ?? '') == $PARENT_SINGULIER->id ? 'selected' : '' }}>
                {{ $PARENT_SINGULIER->nom }}
            </option>
        @endforeach
    </select>
    @error('PARENT_SINGULIER_id')
        <p style="color: red">{{ $message }}</p>
    @enderror
</div>
```

> ⚠️ **Si tu as `Undefined variable $PARENT_PLURIEL`**, tu as oublié de l'envoyer dans `create()` **ou** dans `edit()`.

### R10. Afficher le parent

**Fichiers :** `resources/views/PLURIEL/index.blade.php` (nouvelle colonne `<td>`) et `show.blade.php` (nouvelle ligne `<p>`)

```html
{{ $SINGULIER->PARENT_SINGULIER->nom }}
```

> ⚠️ **Si tu as `Attempt to read property "nom" on null`**, un élément n'a pas de parent. Écris plutôt `{{ $SINGULIER->PARENT_SINGULIER?->nom }}`.
>
> ⚠️ **Si tu fais aussi un CRUD du parent** avec `Route::resource('PARENT_PLURIEL', ...)`, lance `route:list`. Si tu vois `{category}` au lieu de `{PARENT_SINGULIER}`, ajoute `->parameters(['PARENT_PLURIEL' => 'PARENT_SINGULIER'])` (voir l'étape 5).

> ✅ **Vérification finale :** sur `/PLURIEL/create`, la liste déroulante affiche les parents. Après enregistrement, le nom du parent apparaît dans la liste. Si tu envoies le formulaire sans choisir de parent, une erreur rouge s'affiche.
