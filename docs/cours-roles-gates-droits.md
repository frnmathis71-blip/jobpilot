# Cours : rôles, Gates et droits dans JobPilot

Ce cours part **de ton code actuel** et explique, pas à pas, comment Laravel gère
« qui a le droit de faire quoi ». Chaque notion est illustrée par du code tiré de
JobPilot, puis par du code que tu peux ajouter pour aller plus loin.

---

## Sommaire

1. Les deux questions : authentification et autorisation
2. Le rôle dans JobPilot : d'où il vient, où il est stocké
3. Niveau 1 : les middlewares de rôle (le « videur à l'entrée »)
4. Niveau 2 : les Gates (une règle avec un nom)
5. Niveau 3 : les Policies (les règles d'un modèle)
6. Où vérifier un droit : route, contrôleur, FormRequest, vue
7. Cas concrets dans JobPilot (avec le code complet)
8. Tester les droits
9. Bonnes pratiques et récapitulatif

---

## 1. Les deux questions : authentification et autorisation

Toute requête pose deux questions, dans cet ordre :

| Question | Nom | Qui y répond dans JobPilot | Échec |
|---|---|---|---|
| **Qui es-tu ?** | Authentification | Fortify + middleware `auth` | redirection vers `/login` |
| **As-tu le droit ?** | Autorisation | `IsAdmin` / `IsCompany` / `IsCandidat` (+ Gates/Policies à venir) | erreur **403** |

Le chemin d'une requête dans ton projet :

```
Navigateur ──> GET /offer/5/edit
                 │
                 ▼
        middleware 'auth'        ── pas connecté ? ──> redirect /login
                 │
                 ▼
        middleware 'IsCompany'   ── role ≠ company ? ──> 403
                 │
                 ▼
        OfferController@edit     ── (ici, une Policy devrait vérifier
                 │                    que l'offre 5 appartient bien
                 ▼                    à MON entreprise)
             vue Blade           ── @can pour cacher/afficher les boutons
```

Retiens l'idée principale : **un rôle dit « quel type d'utilisateur tu es »,
un droit dit « ce que tu peux faire sur CETTE donnée-là »**. Les middlewares
traitent le premier point, les Gates et Policies traitent le second.

---

## 2. Le rôle dans JobPilot

### 2.1 Stockage : une colonne `role`

`database/migrations/0001_01_01_000000_create_users_table.php`

```php
$table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
$table->foreignId('candidat_id')->nullable()->constrained('candidats')->cascadeOnDelete();
// ...
$table->enum('role', ['candidat', 'company', 'admin'])->default('candidat');
```

- Un utilisateur a **un seul rôle** (`candidat`, `company` ou `admin`).
- Selon le rôle, il est relié à une fiche : `company_id` pour une entreprise,
  `candidat_id` pour un candidat. Ce lien sert plus tard à vérifier l'appartenance
  (« cette offre est-elle à mon entreprise ? »).

### 2.2 Attribution : à l'inscription

`app/Actions/Fortify/CreateNewUser.php`

```php
Validator::make($input, [
    'role' => ['required', Rule::in(['candidat', 'company'])], // pas 'admin' !
    // ...
])->validate();

return DB::transaction(function () use ($input) {
    $data = [/* name, email, password */ 'role' => $input['role']];

    if ($input['role'] === 'company') {
        $data['company_id'] = Company::create()->id;
    } else {
        $data['candidat_id'] = Candidat::create()->id;
    }

    return User::create($data);
});
```

Point de sécurité important : la règle `Rule::in(['candidat', 'company'])`
empêche quelqu'un de s'inscrire comme `admin` en modifiant le formulaire HTML.
**Il ne faut jamais faire confiance à une valeur envoyée par le navigateur.**

> ⚠️ `role` est dans `#[Fillable]` du modèle `User`. C'est sans danger ici, car
> l'inscription valide la valeur. En revanche, si un jour tu écris
> `auth()->user()->update($request->all())` dans la page profil, un utilisateur
> pourra envoyer `role=admin` et devenir administrateur. Il faut toujours passer
> des données **validées** (`$request->validated()`), jamais `$request->all()`.

### 2.3 Lecture : à chaque requête

Il n'y a pas de token. Laravel garde l'id de l'utilisateur en **session**, recharge
le `User` depuis la base de données, et tu lis le rôle avec :

```php
auth()->user()->role      // 'admin' | 'company' | 'candidat'
$request->user()->role    // équivalent, dans un contrôleur ou un middleware
```

### 2.4 Amélioration conseillée : des méthodes sur `User`

Aujourd'hui la chaîne `'admin'` est écrite en dur dans les middlewares, les vues
et les tests. Une faute de frappe (`'amdin'`) donnerait un bug silencieux.
Centralise plutôt la logique dans le modèle :

```php
// app/Models/User.php
public function isAdmin(): bool    { return $this->role === 'admin'; }
public function isCompany(): bool  { return $this->role === 'company'; }
public function isCandidat(): bool { return $this->role === 'candidat'; }

public function hasRole(string ...$roles): bool
{
    return in_array($this->role, $roles, true);
}
```

Utilisation :

```php
if (auth()->user()->isAdmin()) { /* ... */ }
if (auth()->user()->hasRole('admin', 'company')) { /* ... */ }
```

Version encore plus robuste : un **enum PHP**.

```php
// app/Enums/Role.php
namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Company = 'company';
    case Candidat = 'candidat';
}

// app/Models/User.php → dans casts()
'role' => \App\Enums\Role::class,

// Utilisation
if ($user->role === Role::Admin) { /* ... */ }
```

---

## 3. Niveau 1 : les middlewares de rôle

### 3.1 Ce que c'est

Un middleware est une **barrière placée devant une route**. Il s'exécute avant le
contrôleur et peut soit laisser passer la requête (`$next($request)`), soit la bloquer.

Ton `app/Http/Middleware/IsCompany.php` :

```php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check() || auth()->user()->role !== 'company') {
        abort(403, 'Accès interdit');
    }

    return $next($request);
}
```

### 3.2 L'enregistrement : un alias

`bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'IsCandidat' => IsCandidat::class,
        'IsAdmin'    => IsAdmin::class,
        'IsCompany'  => IsCompany::class,
    ]);
})
```

L'alias permet d'écrire `'IsCompany'` dans les routes au lieu du nom complet de la classe.

### 3.3 L'utilisation : sur des groupes de routes

`routes/web.php`

```php
// Entreprises : créer et modifier des offres
Route::middleware(['auth', 'IsCompany'])->group(function () {
    Route::resource('offer', OfferController::class)
        ->only(['create', 'store', 'edit', 'update']);
    Route::resource('jobposting', JobPostingController::class)->only(['index']);
});

// Administration
Route::middleware(['auth', 'IsAdmin'])->group(function () {
    Route::resource('candidat', CandidatController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('company', CompanyController::class);
    Route::resource('offer', OfferController::class)->only(['destroy']);
});

// Candidats : postuler
Route::middleware(['auth', 'IsCandidat'])->group(function () {
    Route::get('/offer/{offer}/jobposting/create', [JobPostingController::class, 'create'])
        ->name('jobposting.create');
    Route::resource('jobposting', JobPostingController::class)->only(['store']);
});

// Tout utilisateur connecté
Route::middleware(['auth'])->group(function () {
    Route::resource('candidat', CandidatController::class)->only(['index', 'show']);
    Route::resource('offer', OfferController::class)->only(['index', 'show']);
});
```

Les middlewares s'exécutent **dans l'ordre du tableau** : `auth` d'abord
(un visiteur est redirigé vers `/login`), puis `IsCompany` (un candidat connecté reçoit une 403).
C'est exactement ce que vérifie `tests/Feature/OfferAccessTest.php`.

### 3.4 Factoriser : un seul middleware avec paramètre

Tes trois middlewares sont identiques, seule la chaîne change. Laravel permet de
passer des **paramètres** à un middleware avec `:` :

```php
// app/Http/Middleware/HasRole.php
class HasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Accès interdit');
        }

        return $next($request);
    }
}

// bootstrap/app.php
$middleware->alias(['role' => \App\Http\Middleware\HasRole::class]);

// routes/web.php
Route::middleware(['auth', 'role:company'])->group(/* ... */);
Route::middleware(['auth', 'role:admin,company'])->group(/* ... */); // plusieurs rôles
```

### 3.5 La limite des middlewares de rôle

Le middleware sait **qui** tu es, mais il ne sait **rien de la donnée** que tu modifies.

Exemple réel dans JobPilot : l'entreprise A (id 1) se connecte et va sur
`/offer/42/edit`, alors que l'offre 42 appartient à l'entreprise B.

- `auth` laisse passer, car A est connectée.
- `IsCompany` laisse passer, car A est une entreprise.
- `OfferController@edit` affiche l'offre et `update` l'enregistre. **A a modifié l'offre de B.**

Pour bloquer ce cas, il faut une règle qui compare l'utilisateur **et** l'offre.
C'est le rôle des Gates et des Policies.

---

## 4. Niveau 2 : les Gates

### 4.1 Ce que c'est

Une Gate est une **règle d'autorisation nommée** : une fonction qui reçoit
l'utilisateur connecté (et éventuellement un objet) et renvoie `true` ou `false`.
On les déclare dans `AppServiceProvider::boot()`.

```php
// app/Providers/AppServiceProvider.php
use App\Models\Offer;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    $this->configureDefaults();
    $this->configureGates();
}

protected function configureGates(): void
{
    // Gate sans objet : basée uniquement sur le rôle
    Gate::define('access-admin', fn (User $user) => $user->role === 'admin');

    Gate::define('publish-offers', fn (User $user) => $user->role === 'company');

    // Gate avec objet : compare l'utilisateur ET la donnée
    Gate::define('manage-offer', function (User $user, Offer $offer) {
        return $user->role === 'company'
            && $user->company_id === $offer->company_id;
    });
}
```

> Laravel injecte **automatiquement** l'utilisateur connecté comme premier
> argument. Tu ne le passes jamais toi-même. Si personne n'est connecté, la Gate
> renvoie `false` sans même être appelée.

### 4.2 Vérifier une Gate

```php
use Illuminate\Support\Facades\Gate;

// Renvoie un booléen
if (Gate::allows('access-admin')) { /* ... */ }
if (Gate::denies('manage-offer', $offer)) { /* ... */ }

// Lance une 403 si refusé (le plus pratique dans un contrôleur)
Gate::authorize('manage-offer', $offer);

// Depuis l'utilisateur
$request->user()->can('manage-offer', $offer);
$request->user()->cannot('access-admin');

// Pour un autre utilisateur que celui connecté
Gate::forUser($autreUser)->allows('access-admin');
```

### 4.3 Message de refus personnalisé

Au lieu d'un booléen, une Gate peut renvoyer une `Response` :

```php
use Illuminate\Auth\Access\Response;

Gate::define('manage-offer', function (User $user, Offer $offer) {
    if ($user->role !== 'company') {
        return Response::deny('Seules les entreprises gèrent des offres.');
    }

    return $user->company_id === $offer->company_id
        ? Response::allow()
        : Response::denyAsNotFound(); // 404 : on ne révèle pas que l'offre existe
});
```

### 4.4 Le « super-admin » : `Gate::before`

`Gate::before` s'exécute **avant toutes les autres règles**. Si elle renvoie
`true` ou `false`, ce résultat est définitif. Si elle renvoie `null`, Laravel
continue avec la règle normale.

```php
Gate::before(function (User $user, string $ability) {
    if ($user->role === 'admin') {
        return true; // l'admin peut tout faire
    }

    return null; // ⚠️ surtout pas false, sinon tout le monde est bloqué
});
```

À utiliser avec prudence : l'admin pourrait alors aussi « postuler » à une offre.
Tu peux exclure certaines actions :

```php
Gate::before(function (User $user, string $ability) {
    if ($user->role === 'admin' && $ability !== 'apply') {
        return true;
    }
    return null;
});
```

### 4.5 Quand utiliser une Gate ?

Une Gate convient pour une action **qui n'est pas liée à un modèle précis** :
accéder au panneau admin, voir les statistiques, exporter en CSV, etc.
Dès que la règle porte sur un modèle (Offer, Company, Candidat), utilise une **Policy**.

---

## 5. Niveau 3 : les Policies

### 5.1 Ce que c'est

Une Policy est une **classe qui regroupe toutes les règles d'un modèle**.
Tu en as déjà trois dans `app/Policies/`, générées par :

```bash
php artisan make:policy OfferPolicy --model=Offer
```

Pour l'instant elles renvoient toutes `false` et ne sont appelées nulle part.
Elles sont donc inactives.

### 5.2 Comment Laravel trouve la Policy

C'est **automatique**, grâce à une convention de nommage :

```
App\Models\Offer     ──>  App\Policies\OfferPolicy
App\Models\Company   ──>  App\Policies\CompanyPolicy
App\Models\Candidat  ──>  App\Policies\CandidatPolicy
```

Quand tu écris `Gate::authorize('update', $offer)`, Laravel voit que `$offer`
est un `Offer`, charge `OfferPolicy` et appelle sa méthode `update($user, $offer)`.
Il n'y a rien à enregistrer.

Si tu ne respectes pas la convention, tu peux la déclarer à la main :

```php
Gate::policy(Offer::class, OfferPolicy::class);
```

### 5.3 Correspondance méthodes ↔ actions du contrôleur

| Méthode de la Policy | Action du contrôleur resource | Reçoit un modèle ? |
|---|---|---|
| `viewAny(User $user)` | `index` | non |
| `view(User $user, Offer $offer)` | `show` | oui |
| `create(User $user)` | `create`, `store` | non (on passe la classe) |
| `update(User $user, Offer $offer)` | `edit`, `update` | oui |
| `delete(User $user, Offer $offer)` | `destroy` | oui |

Tu peux aussi ajouter **tes propres méthodes** (par exemple `apply` pour postuler).

### 5.4 `OfferPolicy` réécrite pour JobPilot

Voici une version qui traduit les règles métier de ton application :

```php
<?php

namespace App\Policies;

use App\Models\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfferPolicy
{
    /**
     * Exécutée AVANT chaque méthode ci-dessous.
     * L'admin a tous les droits, sauf postuler.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin' && $ability !== 'apply') {
            return true;
        }

        return null; // on laisse la méthode normale décider
    }

    /** GET /offer : tout utilisateur connecté voit la liste. */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /** GET /offer/{offer} : tout utilisateur connecté voit le détail. */
    public function view(User $user, Offer $offer): bool
    {
        return true;
    }

    /** GET /offer/create, POST /offer : seulement les entreprises. */
    public function create(User $user): bool
    {
        return $user->role === 'company';
    }

    /** GET /offer/{offer}/edit, PUT /offer/{offer} : l'entreprise propriétaire. */
    public function update(User $user, Offer $offer): Response
    {
        if ($user->role !== 'company') {
            return Response::deny('Seules les entreprises peuvent modifier une offre.');
        }

        return $user->company_id === $offer->company_id
            ? Response::allow()
            : Response::deny("Cette offre n'appartient pas à votre entreprise.");
    }

    /** DELETE /offer/{offer} : l'entreprise propriétaire (l'admin passe par before). */
    public function delete(User $user, Offer $offer): bool
    {
        return $user->role === 'company'
            && $user->company_id === $offer->company_id;
    }

    /** Méthode personnalisée : postuler à une offre. */
    public function apply(User $user, Offer $offer): Response
    {
        if ($user->role !== 'candidat') {
            return Response::deny('Seuls les candidats peuvent postuler.');
        }

        $dejaPostule = $offer->jobposting()->where('user_id', $user->id)->exists();

        return $dejaPostule
            ? Response::deny('Vous avez déjà postulé à cette offre.')
            : Response::allow();
    }
}
```

> 💡 Pour `delete`, tu peux décider que seule l'administration supprime
> (ce que fait ta route actuelle). Dans ce cas, `delete()` renvoie simplement
> `false` et l'admin passe quand même grâce à `before()`.

### 5.5 Gate ou Policy : comment choisir ?

| Situation | Outil |
|---|---|
| « Seuls les admins voient le tableau de bord admin » | Gate `access-admin` (ou middleware) |
| « Une entreprise ne modifie que SES offres » | Policy `OfferPolicy::update` |
| « Un candidat ne postule qu'une fois » | Policy `OfferPolicy::apply` |
| « Une entreprise ne voit que les candidatures de SES offres » | Policy + filtre dans la requête SQL |

---

## 6. Où vérifier un droit

Il y a quatre endroits possibles. Ils sont complémentaires.

### 6.1 Sur la route (middleware `can`)

Laravel fournit un middleware `can` qui appelle la Policy :

```php
// 'offer' est le nom du paramètre de route {offer}
Route::put('/offer/{offer}', [OfferController::class, 'update'])
    ->middleware('can:update,offer');

// Syntaxe plus lisible, équivalente
Route::put('/offer/{offer}', [OfferController::class, 'update'])
    ->can('update', 'offer');

// Action sans modèle : on passe la classe
Route::get('/offer/create', [OfferController::class, 'create'])
    ->can('create', \App\Models\Offer::class);
```

### 6.2 Dans le contrôleur (`Gate::authorize`)

C'est la méthode la plus claire et la plus courante.

> Dans Laravel 11 et plus, le `Controller` de base est vide (c'est le cas dans
> JobPilot). `$this->authorize()` n'existe donc pas, sauf si tu ajoutes le trait
> `AuthorizesRequests`. Utilise `Gate::authorize()`.

```php
use Illuminate\Support\Facades\Gate;

public function edit(Offer $offer)
{
    Gate::authorize('update', $offer); // 403 si ce n'est pas MON offre
    // ...
}

public function create()
{
    Gate::authorize('create', Offer::class); // pas d'instance : on passe la classe
    // ...
}
```

Pour garder `$this->authorize(...)`, ajoute le trait au contrôleur de base :

```php
// app/Http/Controllers/Controller.php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller
{
    use AuthorizesRequests;
}

// puis dans n'importe quel contrôleur :
$this->authorize('update', $offer);
```

### 6.3 Dans une FormRequest (`authorize()`)

Tes `StoreOfferRequest` et `UpdateOfferRequest` renvoient `true`, c'est-à-dire
« tout le monde est autorisé ». Tu peux y brancher la Policy :

```php
// app/Http/Requests/UpdateOfferRequest.php
public function authorize(): bool
{
    // $this->route('offer') = l'Offer résolue depuis {offer} dans l'URL
    return $this->user()->can('update', $this->route('offer'));
}

public function rules(): array
{
    return [
        'Name'        => ['required', 'string', 'max:255'],
        'Salary'      => ['required', 'integer', 'min:0'],
        'Description' => ['required', 'string'],
        // company_id n'est PAS ici : on ne laisse pas l'utilisateur le choisir
    ];
}
```

```php
// app/Http/Requests/StoreOfferRequest.php
public function authorize(): bool
{
    return $this->user()->can('create', \App\Models\Offer::class);
}
```

Si `authorize()` renvoie `false`, Laravel renvoie une 403 **avant** même
d'entrer dans le contrôleur.

### 6.4 Dans les vues Blade (`@can`)

La vue sert **uniquement à afficher ou cacher** des boutons. Elle ne protège rien :
un utilisateur peut toujours taper l'URL ou envoyer le formulaire à la main.
La vraie protection reste sur la route, dans le contrôleur ou dans la FormRequest.

```blade
@can('update', $offer)
    <a href="{{ route('offer.edit', $offer) }}">Modifier</a>
@endcan

@can('delete', $offer)
    <form action="{{ route('offer.destroy', $offer) }}" method="POST">
        @csrf @method('DELETE')
        <button type="submit">Supprimer</button>
    </form>
@endcan

@can('create', App\Models\Offer::class)
    <a href="{{ route('offer.create') }}">Ajouter une offre</a>
@endcan

@cannot('apply', $offer)
    <span>Déjà postulé</span>
@endcannot

@canany(['update', 'delete'], $offer)
    <div class="actions">...</div>
@endcanany

{{-- Avec une Gate --}}
@can('access-admin')
    <a href="{{ route('company.index') }}">Entreprises</a>
@endcan
```

### 6.5 Récapitulatif des endroits

| Endroit | Rôle | Protège vraiment ? |
|---|---|---|
| Middleware de route (`auth`, `IsCompany`, `can:`) | Filtre grossier, avant le contrôleur | ✅ oui |
| `Gate::authorize()` dans le contrôleur | Règle fine sur la donnée | ✅ oui |
| `FormRequest::authorize()` | Règle fine + validation au même endroit | ✅ oui |
| `@can` dans Blade | Afficher / cacher des éléments | ❌ non, c'est de l'affichage |

---

## 7. Cas concrets dans JobPilot

### Cas n°1 : une entreprise ne modifie que ses propres offres

**Problème actuel** : `OfferController@edit` et `@update` ne vérifient pas
l'appartenance, et le formulaire laisse choisir `company_id` dans une liste
de **toutes** les entreprises (`Company::all()`).

**Correction** :

```php
// app/Http/Controllers/OfferController.php
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

public function create()
{
    Gate::authorize('create', Offer::class);

    return view('offer.create'); // plus besoin de la liste des entreprises
}

public function store(Request $request)
{
    Gate::authorize('create', Offer::class);

    $validated = $request->validate([
        'Name'        => 'required|string|max:255',
        'Salary'      => 'required|integer|min:0',
        'Description' => 'required|string',
    ]);

    // L'entreprise est imposée par le serveur, jamais lue dans le formulaire.
    $validated['company_id'] = $request->user()->company_id;

    Offer::create($validated);

    return redirect()->route('offer.index')->with('success', 'Offre ajoutée avec succès');
}

public function edit(Offer $offer)
{
    Gate::authorize('update', $offer);

    return view('offer.edit', compact('offer'));
}

public function update(Request $request, Offer $offer)
{
    Gate::authorize('update', $offer);

    $offer->update($request->validate([
        'Name'        => 'required|string|max:255',
        'Salary'      => 'required|integer|min:0',
        'Description' => 'required|string',
    ]));

    return redirect()->route('offer.index')->with('success', 'Offre modifiée avec succès');
}

public function destroy(Offer $offer)
{
    Gate::authorize('delete', $offer);

    $offer->delete();

    return redirect()->route('offer.index')->with('success', 'Offre supprimée avec succès');
}
```

Variante avec une **relation** (encore plus sûre) : on crée l'offre « à travers »
l'entreprise de l'utilisateur, ce qui remplit `company_id` automatiquement.

```php
$request->user()->company->offers()->create($validated);
```

### Cas n°2 : la liste des offres affiche les bons boutons

**Problème actuel** dans `resources/views/offer/index.blade.php` : le bloc
`@if (in_array(auth()->user()->role, ['admin', 'company']))` affiche à la fois
« Supprimer » (route admin, donc 403 pour une entreprise) et « Modifier » /
« Ajouter » (routes entreprise, donc 403 pour un admin). De plus, une entreprise
voit « Modifier » sur les offres des autres.

**Correction** : un seul affichage, où chaque bouton dépend de la Policy.

```blade
<x-layouts::jobpilot title="Offres" heading="Offres d'emploi">

    @if (session('success'))
        <p>{{ session('success') }}</p>
    @endif

    @can('create', App\Models\Offer::class)
        <a href="{{ route('offer.create') }}">Ajouter une offre</a>
    @endcan

    @foreach ($offers as $offer)
        <section>
            Offre N°{{ $offer->id }} {{ $offer->Name }} <br>
            Salaire : {{ $offer->Salary }} <br>
            Entreprise : {{ $offer->company->name }} <br>

            <a href="{{ route('offer.show', $offer) }}">Voir</a>

            @can('update', $offer)
                <a href="{{ route('offer.edit', $offer) }}">Modifier</a>
            @endcan

            @can('delete', $offer)
                <form action="{{ route('offer.destroy', $offer) }}" method="POST">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
                </form>
            @endcan

            @can('apply', $offer)
                <a href="{{ route('jobposting.create', $offer) }}">Postuler</a>
            @elsecan('view', $offer)
                @if (auth()->user()->role === 'candidat')
                    <em>Déjà postulé</em>
                @endif
            @endcan
        </section>
    @endforeach

</x-layouts::jobpilot>
```

Résultat :

| Utilisateur | Ajouter | Modifier | Supprimer | Postuler |
|---|---|---|---|---|
| admin | ✅ (via `before`) | ✅ | ✅ | ❌ (exclu de `before`) |
| entreprise A, offre de A | ✅ | ✅ | selon ton choix pour `delete` | ❌ |
| entreprise A, offre de B | ✅ | ❌ | ❌ | ❌ |
| candidat, pas encore postulé | ❌ | ❌ | ❌ | ✅ |
| candidat, déjà postulé | ❌ | ❌ | ❌ | ❌ « Déjà postulé » |

> Si tu gardes le middleware `IsCompany` sur `offer.create`, l'admin verra le
> bouton mais recevra une 403. Il faut que **route et Policy disent la même chose**.
> Le plus simple : retirer `IsCompany` de ces routes et laisser la Policy décider
> (voir cas n°4).

### Cas n°3 : postuler (candidat)

```php
// app/Http/Controllers/JobPostingController.php
use Illuminate\Support\Facades\Gate;

public function create(Offer $offer)
{
    Gate::authorize('apply', $offer);

    return view('jobposting.create', compact('offer'));
}

public function store(Request $request)
{
    $validated = $request->validate([
        'offer_id' => 'required|exists:offers,id',
    ]);

    $offer = Offer::findOrFail($validated['offer_id']);

    // Message clair : « Vous avez déjà postulé à cette offre. »
    Gate::authorize('apply', $offer);

    JobPosting::create([
        'offer_id' => $offer->id,
        'user_id'  => $request->user()->id,
    ]);

    return redirect()->route('offer.index')->with('success', 'Candidature envoyée avec succès');
}
```

Le message défini dans `Response::deny('Vous avez déjà postulé...')` s'affiche
sur la page d'erreur 403.

### Cas n°4 : simplifier `routes/web.php` avec les Policies

Une fois les Policies en place, les règles de rôle et d'appartenance y sont
toutes regroupées. Les routes n'ont plus besoin que de `auth` :

```php
Route::middleware(['auth'])->group(function () {
    Route::resource('offer', OfferController::class);
    Route::get('/offer/{offer}/jobposting/create', [JobPostingController::class, 'create'])
        ->name('jobposting.create');
    Route::resource('jobposting', JobPostingController::class)->only(['index', 'store']);
});

Route::middleware(['auth', 'can:access-admin'])->group(function () {
    Route::resource('company', CompanyController::class);
    Route::resource('candidat', CandidatController::class)->except(['index', 'show']);
});
```

Avantages :
- plus de problème d'**ordre des routes** (`/offer/create` contre `/offer/{offer}`),
  puisque la resource est déclarée en une seule fois ;
- la règle « qui peut modifier une offre » existe **à un seul endroit** (`OfferPolicy`).

Tu peux aussi garder les middlewares de rôle comme **première barrière**
et ajouter les Policies comme seconde barrière. Les deux approches sont correctes,
mais il faut qu'elles soient cohérentes.

### Cas n°5 : `authorizeResource` en une ligne

Pour un contrôleur resource classique, on peut relier toutes les actions
à la Policy d'un seul coup. Il faut le trait `AuthorizesRequests` (voir 6.2)
et implémenter `HasMiddleware` :

```php
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CompanyController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:viewAny,App\Models\Company', only: ['index']),
            new Middleware('can:view,company',               only: ['show']),
            new Middleware('can:create,App\Models\Company',  only: ['create', 'store']),
            new Middleware('can:update,company',             only: ['edit', 'update']),
            new Middleware('can:delete,company',             only: ['destroy']),
        ];
    }
    // ... les méthodes n'ont plus besoin d'appeler Gate::authorize()
}
```

### Cas n°6 : filtrer les données (droits sur des listes)

Une Policy répond à la question « ai-je le droit sur **cet** objet ? ». Pour une
**liste**, on ne vérifie pas chaque ligne : on filtre directement en SQL. Ton
`JobPostingController@index` le fait déjà bien :

```php
$offers = Offer::where('company_id', auth()->user()->company_id)
    ->with('jobposting.users')
    ->get();
```

Pour réutiliser ce filtre, crée un **scope** sur le modèle :

```php
// app/Models/Offer.php
public function scopeVisibleTo($query, User $user)
{
    return match ($user->role) {
        'admin'    => $query,                                        // tout
        'company'  => $query->where('company_id', $user->company_id), // les siennes
        'candidat' => $query,                                        // toutes les offres publiées
    };
}

// Contrôleur
$offers = Offer::visibleTo($request->user())->get();
```

### Cas n°7 : le menu du layout

`resources/views/layouts/jobpilot.blade.php` teste le rôle en dur :

```blade
@if ($role === 'admin')
    <a href="{{ route('company.index') }}">Entreprises</a>
@endif
```

Avec une Gate ou une Policy, le menu suit automatiquement les règles :

```blade
@can('access-admin')
    <a href="{{ route('company.index') }}">Entreprises</a>
@endcan

@can('viewAny', App\Models\JobPosting::class)
    <a href="{{ route('jobposting.index') }}">Candidatures</a>
@endcan
```

Si un jour tu changes la règle (par exemple un rôle `recruteur` qui voit aussi
les candidatures), il suffit de modifier la Policy : le menu, la route et le
contrôleur suivent sans autre changement.

---

## 8. Tester les droits

Les droits sont la partie du code où un bug coûte le plus cher. Teste chaque
combinaison rôle × action. Exemple dans le style de `tests/Feature/OfferAccessTest.php` :

```php
<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function companyUser(): User
    {
        return User::factory()->create([
            'role' => 'company',
            'company_id' => Company::create()->id,
        ]);
    }

    private function offerOf(User $owner): Offer
    {
        return Offer::create([
            'company_id'  => $owner->company_id,
            'Name'        => 'Dev PHP',
            'Salary'      => 40000,
            'Description' => 'Laravel',
        ]);
    }

    public function test_company_can_edit_its_own_offer(): void
    {
        $owner = $this->companyUser();
        $offer = $this->offerOf($owner);

        $this->actingAs($owner)
            ->get(route('offer.edit', $offer))
            ->assertOk();
    }

    public function test_company_cannot_edit_another_company_offer(): void
    {
        $owner = $this->companyUser();
        $other = $this->companyUser();
        $offer = $this->offerOf($owner);

        $this->actingAs($other)
            ->put(route('offer.update', $offer), [
                'Name' => 'Piratée', 'Salary' => 1, 'Description' => 'x',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'Name' => 'Dev PHP']);
    }

    public function test_store_ignores_company_id_sent_by_the_form(): void
    {
        $me = $this->companyUser();
        $other = $this->companyUser();

        $this->actingAs($me)->post(route('offer.store'), [
            'company_id' => $other->company_id, // tentative de triche
            'Name' => 'Test', 'Salary' => 30000, 'Description' => 'x',
        ]);

        $this->assertDatabaseHas('offers', ['Name' => 'Test', 'company_id' => $me->company_id]);
    }

    public function test_candidat_cannot_apply_twice(): void
    {
        $offer = $this->offerOf($this->companyUser());
        $candidat = User::factory()->create(['role' => 'candidat']);

        $this->assertTrue($candidat->can('apply', $offer));

        $offer->jobposting()->create(['user_id' => $candidat->id]);

        $this->assertFalse($candidat->fresh()->can('apply', $offer));
    }

    public function test_admin_can_do_everything_except_apply(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $offer = $this->offerOf($this->companyUser());

        $this->assertTrue($admin->can('update', $offer));
        $this->assertTrue($admin->can('delete', $offer));
        $this->assertFalse($admin->can('apply', $offer));
    }
}
```

Tu peux aussi tester une Policy **directement**, sans passer par HTTP :

```php
$this->assertTrue((new \App\Policies\OfferPolicy)->create($companyUser));
```

Lancer les tests :

```bash
php artisan test --filter=OfferPolicyTest
```

---

## 9. Bonnes pratiques et récapitulatif

### Les règles d'or

1. **Le serveur décide, jamais le navigateur.** Ne lis jamais `role` ni
   `company_id` depuis le formulaire pour une décision de droit. Prends-les
   dans `auth()->user()`.
2. **Cacher n'est pas protéger.** `@can` dans une vue est un confort
   d'affichage. Il faut toujours une vérification côté route, contrôleur ou FormRequest.
3. **Une règle, un seul endroit.** Mets « qui peut modifier une offre » dans
   `OfferPolicy::update` et réutilise-la partout (`can:`, `Gate::authorize`, `@can`).
4. **Le rôle ne suffit pas.** Le rôle dit « tu es une entreprise ». Le droit dit
   « c'est TON offre ». Il faut presque toujours vérifier les deux.
5. **Refuser par défaut.** Une méthode de Policy qui manque, ou qui renvoie
   `null`, équivaut à un refus. C'est le comportement sûr : ne le contourne pas.
6. **Teste les refus**, pas seulement les accès autorisés.

### Tableau mémo

| Je veux… | J'écris… |
|---|---|
| Exiger d'être connecté | `->middleware('auth')` |
| Réserver une route à un rôle | `->middleware('IsCompany')` ou `role:company` |
| Définir une règle sans modèle | `Gate::define('access-admin', fn (User $u) => ...)` |
| Définir les règles d'un modèle | `php artisan make:policy OfferPolicy --model=Offer` |
| Donner tous les droits à l'admin | méthode `before()` dans la Policy, ou `Gate::before()` |
| Protéger une route avec une Policy | `->can('update', 'offer')` |
| Protéger dans le contrôleur | `Gate::authorize('update', $offer);` |
| Protéger dans une FormRequest | `return $this->user()->can('update', $this->route('offer'));` |
| Tester un droit en PHP | `$user->can('update', $offer)` / `Gate::allows(...)` |
| Afficher un bouton selon le droit | `@can('update', $offer) ... @endcan` |
| Action sans instance (create) | passer la classe : `Offer::class` |
| Refuser avec un message | `return Response::deny('...');` |
| Cacher l'existence d'une ressource | `return Response::denyAsNotFound();` |
| Filtrer une liste | scope Eloquent (`Offer::visibleTo($user)`) |

### État de JobPilot et prochaines étapes

| Élément | État actuel | À faire |
|---|---|---|
| Colonne `role` + inscription | ✅ en place, `admin` bloqué à l'inscription | méthodes `isAdmin()` ou enum `Role` |
| Middlewares `IsAdmin/IsCompany/IsCandidat` | ✅ utilisés dans `routes/web.php` | éventuellement un seul `role:` paramétré |
| Gates | ❌ aucune | `access-admin` pour l'espace administration |
| Policies | ⚠️ squelettes à `false`, non appelées | écrire `OfferPolicy` (cas n°1 à n°3) |
| Appartenance des offres | ❌ une entreprise peut modifier l'offre d'une autre | `Gate::authorize('update', $offer)` + `company_id` imposé |
| FormRequests | ⚠️ `authorize()` = `true`, `rules()` vides | y mettre `can(...)` et les règles de validation |
| Vues | ⚠️ `in_array($role, ...)` affiche des boutons qui mènent à une 403 | remplacer par `@can` |
| `/dashboard` | ❌ pas de middleware `auth` | `->middleware(['auth', 'verified'])` |
