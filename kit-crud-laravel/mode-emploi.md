---
title: "Kit CRUD Laravel : mode d'emploi"
subtitle: "Un projet prêt à l'emploi avec deux modèles reliés (un PARENT a plusieurs ENFANTS). Il suffit de remplacer les noms."
lang: fr
---

## 1. Le principe en 30 secondes

Presque tous les sujets d'évaluation ont la même forme : **deux tables reliées**, l'une « possède » plusieurs éléments de l'autre.

| PARENT (a plusieurs…) | ENFANT (appartient à un…) | Clé étrangère (dans la table ENFANT) |
|---|---|---|
| Coach | Joueur | `coach_id` |
| Equipe | Joueur | `equipe_id` |
| Client | Commande | `client_id` |
| Categorie | Produit | `categorie_id` |
| Auteur | Livre | `auteur_id` |
| Classe | Eleve | `classe_id` |

> 💡 **Comment savoir qui est le PARENT ?** Fais la phrase « chaque ENFANT appartient à UN seul PARENT ». Par exemple, « chaque joueur a UN coach » : le coach est le PARENT. C'est toujours l'ENFANT qui porte la colonne `PARENT_id`.

Le kit contient **tout le code des deux CRUD** (liste, ajout, détail, modification, suppression), plus la relation entre les deux. Les noms sont écrits en MAJUSCULES (`MODELE`, `PLURIEL`…) et le script `preparer.php` les remplace par tes vrais noms en une seule commande.

## 2. Ce qu'il y a dans le kit

```text
kit-crud-laravel/
├── noms.php              ← LE SEUL FICHIER À REMPLIR
├── preparer.php          ← le script qui installe tout
├── mode-emploi.pdf       ← ce document
├── fichiers/             ← le CRUD PARENT + ENFANT (avec les noms à remplacer)
│   ├── app/Models/                PARENT_MODELE.php, MODELE.php
│   ├── app/Http/Controllers/      PARENT_MODELEController.php, MODELEController.php
│   ├── database/migrations/       ..._create_PARENT_PLURIEL_table.php, ..._create_PLURIEL_table.php
│   ├── database/factories/        PARENT_MODELEFactory.php, MODELEFactory.php
│   ├── database/seeders/          PARENT_MODELESeeder.php, MODELESeeder.php, DatabaseSeeder.php
│   ├── resources/views/layouts/   crud.blade.php
│   ├── resources/views/PARENT_PLURIEL/   _form, index, create, edit, show
│   ├── resources/views/PLURIEL/          _form, index, create, edit, show
│   └── routes/web.php
└── fichiers-roles/       ← OPTION connexion + rôles (middleware, policies, routes protégées)
```

## 3. Les mots à remplacer (fichier `noms.php`)

| Mot | Ce que c'est | Exemple Coach/Joueur | Exemple Client/Commande |
|---|---|---|---|
| `PARENT_MODELE` | modèle parent (Majuscule) | `Coach` | `Client` |
| `PARENT_PLURIEL` | table parent (pluriel, minuscules) | `coachs` | `clients` |
| `PARENT_SINGULIER` | parent au singulier, minuscules | `coach` | `client` |
| `PARENT_CHAMP` | colonne texte du parent | `nom` | `nom` |
| `MODELE` | modèle enfant (Majuscule) | `Joueur` | `Commande` |
| `PLURIEL` | table enfant (pluriel, minuscules) | `joueurs` | `commandes` |
| `SINGULIER` | enfant au singulier, minuscules | `joueur` | `commande` |
| `CHAMP1` | colonne texte de l'enfant | `nom` | `reference` |
| `CHAMP2` | colonne nombre de l'enfant | `numero` | `montant` |
| `NOM_MIDDLEWARE` | (option rôles) classe du middleware | `IsCoach` | `IsVendeur` |
| `ROLE` | (option rôles) rôle qui peut tout gérer | `coach` | `vendeur` |

> ⚠️ **Pas d'accents, pas d'espaces** dans les noms (`Eleve`, et non `Élève`). Les modèles commencent par une majuscule, tout le reste est en minuscules.

## 4. Méthode rapide (avec le script) : à suivre pendant l'éval

### Étape 1 : Créer le projet

```bash
composer create-project laravel/laravel NOM_PROJET
```

```bash
cd NOM_PROJET
```

Vérifie que `.env` contient `DB_CONNECTION=sqlite` (c'est le cas par défaut).

> ✅ `php artisan serve` puis http://127.0.0.1:8000 : la page Laravel s'affiche.

### Étape 2 : Remplir `noms.php`

**Fichier :** `kit-crud-laravel/noms.php`. Remplace les valeurs à droite des `=>` (voir le tableau du §3).

```php
'PARENT_MODELE'    => 'Coach',
'PARENT_PLURIEL'   => 'coachs',
'PARENT_SINGULIER' => 'coach',
'PARENT_CHAMP'     => 'nom',
'MODELE'           => 'Joueur',
'PLURIEL'          => 'joueurs',
'SINGULIER'        => 'joueur',
'CHAMP1'           => 'nom',
'CHAMP2'           => 'numero',
```

### Étape 3 : Lancer le script

Depuis le dossier du kit, en donnant le chemin du projet :

```bash
php preparer.php CHEMIN_DU_PROJET
```

Par exemple, si le kit et le projet sont côte à côte : `php preparer.php ../NOM_PROJET`

> ✅ Le script affiche la liste des fichiers créés (`✔ app/Models/Coach.php`…) et termine par « ✅ Terminé ». S'il affiche « ❌ … », il te dit quelle ligne de `noms.php` corriger.

### Étape 4 : Créer les tables et les données de test

Dans le dossier du projet :

```bash
php artisan migrate:fresh --seed
```

> ✅ Tu dois voir les migrations `create_coachs_table` et `create_joueurs_table` en « DONE », puis les seeders. Cela crée 5 parents et 20 enfants.

### Étape 5 : Tester

```bash
php artisan serve
```

> ✅ **Vérification :**
>
> 1. http://127.0.0.1:8000/coachs affiche 5 coachs avec leur nombre de joueurs.
> 2. http://127.0.0.1:8000/joueurs affiche 20 joueurs avec le nom de leur coach.
> 3. « Ajouter » un joueur : la liste déroulante propose les coachs. Si tu envoies le formulaire vide, des erreurs rouges s'affichent.
> 4. « Voir » un coach : la liste de ses joueurs s'affiche.
> 5. Modifier et supprimer fonctionnent. Supprimer un coach supprime aussi ses joueurs.

> 💡 **Tu t'es trompé de noms ?** Corrige `noms.php`, relance le script, supprime à la main les anciens fichiers mal nommés, puis relance `php artisan migrate:fresh --seed`. Le script remplace le bloc de routes au lieu de l'ajouter une deuxième fois.

## 5. Méthode manuelle (si tu ne peux pas lancer le script)

1. **Copie** le contenu du dossier `fichiers/` dans ton projet (fusionne les dossiers `app`, `database`, `resources` et `routes`).
2. **Renomme les fichiers** qui contiennent des MAJUSCULES : `PARENT_MODELE.php` devient `Coach.php`, `MODELEController.php` devient `JoueurController.php`, le dossier `views/PLURIEL` devient `views/joueurs`, etc.
3. Dans VS Code, ouvre **Rechercher/Remplacer dans tous les fichiers** (`Ctrl+Shift+H`), **active « Aa »** (respect de la casse) et remplace **dans cet ordre** :
   1. `PARENT_MODELE`, puis `PARENT_PLURIEL`, puis `PARENT_SINGULIER`, puis `PARENT_CHAMP`
   2. `MODELE`, puis `PLURIEL`, puis `SINGULIER`, puis `CHAMP1`, puis `CHAMP2`
4. **Routes :** si ton `routes/web.php` contenait déjà des routes, ne l'écrase pas. Copie seulement le bloc entre `// >>> KIT CRUD (début)` et `// <<< KIT CRUD (fin)` à la fin de ton fichier.

> ⚠️ **L'ordre est obligatoire.** Si tu remplaces `MODELE` en premier, `PARENT_MODELE` devient `PARENT_Joueur` et plus rien ne marche.

## 6. Quel fichier fait quoi

| Fichier (exemple Coach/Joueur) | Rôle | Ce que tu modifies si le sujet change |
|---|---|---|
| `database/migrations/2026_01_01_000001_create_coachs_table.php` | crée la table parent | ajouter des colonnes au parent |
| `database/migrations/2026_01_01_000002_create_joueurs_table.php` | crée la table enfant + `coach_id` | ajouter des colonnes à l'enfant |
| `app/Models/Coach.php` | `$fillable` + `hasMany` (`$coach->joueurs`) | `$fillable` |
| `app/Models/Joueur.php` | `$fillable` + `belongsTo` (`$joueur->coach`) | `$fillable` |
| `database/factories/*Factory.php` | fausses données (`fake()`) | une ligne par colonne |
| `database/seeders/*Seeder.php` | combien d'éléments créer (5 et 20) | `count(…)` |
| `database/seeders/DatabaseSeeder.php` | lance les seeders, **le parent d'abord** | rien |
| `app/Http/Controllers/CoachController.php` | les 7 méthodes du parent | règles de `validate()` |
| `app/Http/Controllers/JoueurController.php` | les 7 méthodes de l'enfant + la liste des parents pour le menu déroulant | règles de `validate()` |
| `resources/views/layouts/crud.blade.php` | menu + message vert | rien |
| `resources/views/*/_form.blade.php` | champs du formulaire (ajout + modif) | un bloc `<div>` par colonne |
| `resources/views/*/index.blade.php` | tableau de la liste | une colonne `<th>`/`<td>` |
| `resources/views/*/show.blade.php` | page détail | une ligne `<p>` |
| `routes/web.php` (bloc KIT CRUD) | les 14 routes (7 + 7) | rien |

Trois choses sont déjà réglées dans le kit pour éviter les pièges des noms français :

- `protected $table = '...'` dans chaque modèle, pour que Laravel ne devine pas un mauvais pluriel.
- `->parameters([...])` sur les routes, pour que `{coach}` soit bien le nom utilisé dans le contrôleur.
- `->cascadeOnDelete()` sur la clé étrangère, pour que supprimer un parent ne plante pas.

## 7. Les URL générées

| Action | Parent (ex. coachs) | Enfant (ex. joueurs) | Méthode |
|---|---|---|---|
| Liste | `GET /coachs` | `GET /joueurs` | `index()` |
| Formulaire d'ajout | `GET /coachs/create` | `GET /joueurs/create` | `create()` |
| Enregistrer | `POST /coachs` | `POST /joueurs` | `store()` |
| Détail | `GET /coachs/{coach}` | `GET /joueurs/{joueur}` | `show()` |
| Formulaire de modif | `GET /coachs/{coach}/edit` | `GET /joueurs/{joueur}/edit` | `edit()` |
| Mettre à jour | `PUT /coachs/{coach}` | `PUT /joueurs/{joueur}` | `update()` |
| Supprimer | `DELETE /coachs/{coach}` | `DELETE /joueurs/{joueur}` | `destroy()` |

```bash
php artisan route:list --except-vendor
```

## 8. Adapter le kit pendant l'éval

### 8a. Ajouter une colonne (exemple : `poste` pour un joueur)

Il faut toucher **7 endroits**. Oublier l'un d'eux est l'erreur n°1.

**1. Migration enfant :** `database/migrations/..._create_joueurs_table.php`

```php
$table->string('poste');
```

**2. Modèle :** `app/Models/Joueur.php`, dans `$fillable`

```php
'poste',
```

**3. Factory :** `database/factories/JoueurFactory.php`

```php
'poste' => fake()->word(),
```

**4 et 5. Contrôleur :** `app/Http/Controllers/JoueurController.php`, dans `validate([...])` de **`store()` ET de `update()`**

```php
'poste' => 'required|string|max:255',
```

**6. Formulaire :** `resources/views/joueurs/_form.blade.php`

```html
<div>
    <label for="poste">poste</label>
    <input type="text" id="poste" name="poste"
           value="{{ old('poste', $joueur->poste ?? '') }}">
    @error('poste')
        <p class="erreur">{{ $message }}</p>
    @enderror
</div>
```

**7. Affichage :** dans `index.blade.php`, ajoute `<th>poste</th>` et `<td>{{ $joueur->poste }}</td>`. Dans `show.blade.php`, ajoute `<p>poste : {{ $joueur->poste }}</p>`.

**Puis :**

```bash
php artisan migrate:fresh --seed
```

> ✅ Le champ apparaît dans le formulaire, il est obligatoire, et la valeur s'affiche dans la liste.

### 8b. Changer le type d'une colonne

| Type | Migration | Formulaire | Validation | Factory |
|---|---|---|---|---|
| Texte court | `$table->string('x');` | `type="text"` | `required|string|max:255` | `fake()->word()` |
| Texte long | `$table->text('x');` | `<textarea name="x">{{ old('x', $e->x ?? '') }}</textarea>` | `required|string` | `fake()->paragraph()` |
| Entier | `$table->integer('x');` | `type="number"` | `required|integer` | `fake()->numberBetween(1, 100)` |
| Décimal | `$table->decimal('x', 8, 2);` | `type="number" step="0.01"` | `required|numeric` | `fake()->randomFloat(2, 1, 100)` |
| Date | `$table->date('x');` | `type="date"` | `required|date` | `fake()->date()` |
| E-mail | `$table->string('x');` | `type="email"` | `required|email` | `fake()->email()` |
| Prénom / nom | `$table->string('x');` | `type="text"` | `required|string|max:255` | `fake()->firstName()` / `fake()->lastName()` |
| Facultatif | ajoute `->nullable()` | – | `nullable` au lieu de `required` | – |

### 8c. Un seul modèle, sans relation

Ne donne pas le même nom au PARENT et à l'ENFANT : le script refuse. Remplis `noms.php` avec ton modèle comme PARENT (et un ENFANT au choix), lance le script, puis utilise seulement le CRUD du PARENT : il n'a pas de clé étrangère. Le CRUD de l'ENFANT peut rester inutilisé.

### 8d. Utiliser la relation ailleurs

```php
$coach->joueurs            // tous les joueurs d'un coach (hasMany)
$coach->joueurs->count()   // leur nombre
$joueur->coach             // le coach d'un joueur (belongsTo)
$joueur->coach->nom        // son nom
Joueur::with('coach')->get()  // charger les joueurs ET leur coach en une fois
```

## 9. Option `--roles` : connexion, rôles, middleware, Gate et Policy

> ⚠️ **Prérequis :** le projet doit déjà avoir la connexion (Fortify ou starter kit, comme JobPilot). Avec un projet `composer create-project` « nu », il n'y a pas de page `/login`. Le script te prévient dans ce cas.

### 9a. Lancer

```bash
php preparer.php CHEMIN_DU_PROJET --roles
```

```bash
php artisan migrate:fresh --seed
```

### 9b. Ce que l'option fait

| Quoi | Où | Effet |
|---|---|---|
| Colonne `role` | migration `create_users_table` | `$table->string('role')->default('user');` |
| Middleware `NOM_MIDDLEWARE` | `app/Http/Middleware/` | laisse passer `ROLE` et `admin`, sinon 403 |
| Alias du middleware | `bootstrap/app.php` | permet d'écrire `->middleware('IsCoach')` |
| Gate `acces-admin` | `app/Providers/AppServiceProvider.php` | `@can('acces-admin')` / `Gate::authorize('acces-admin')` |
| 2 Policies | `app/Policies/` | `before()` : l'admin a tout ; `create`/`update`/`delete` réservés à `ROLE` |
| Contrôleurs | `Gate::authorize(...)` dans `create`, `store`, `edit`, `update`, `destroy` | 403 si interdit |
| Routes | bloc KIT CRUD | groupe `auth` + `NOM_MIDDLEWARE` pour modifier, groupe `auth` pour consulter |
| 3 comptes de test | `DatabaseSeeder` | `admin@example.com`, `ROLE@example.com` (ex. `coach@example.com`), `user@example.com` (mot de passe : `password`) |

### 9c. Vérification

| Connecté en tant que | `/joueurs` (liste) | `/joueurs/create` | Supprimer |
|---|---|---|---|
| pas connecté | redirection `/login` | redirection `/login` | redirection `/login` |
| `user@example.com` | ✅ | ❌ 403 | ❌ 403 |
| `coach@example.com` (ROLE) | ✅ | ✅ | ✅ |
| `admin@example.com` | ✅ | ✅ | ✅ |

### 9d. Si le script affiche « ⚠ À FAIRE À LA MAIN »

**Alias** (`bootstrap/app.php`, dans `->withMiddleware(function (Middleware $middleware): void {`) :

```php
        $middleware->alias([
            'NOM_MIDDLEWARE' => \App\Http\Middleware\NOM_MIDDLEWARE::class,
        ]);
```

**Gate** (`app/Providers/AppServiceProvider.php`, dans `boot()`) :

```php
        \Illuminate\Support\Facades\Gate::define('acces-admin', function (\App\Models\User $user) {
            return $user->role === 'admin';
        });
```

**Colonne role** (migration `create_users_table`, sous `$table->string('password');`) :

```php
            $table->string('role')->default('user');
```

### 9e. « Seulement SES éléments » (Policy de propriétaire)

Si le sujet dit « un coach ne modifie que SES joueurs », il faut relier l'élément à l'utilisateur :

1. Dans la migration de l'enfant, ajoute `$table->foreignId('user_id')->constrained('users');`.
2. Dans `$fillable`, ajoute `'user_id'`. Dans la factory, ajoute `'user_id' => \App\Models\User::inRandomOrder()->first()->id,`.
3. Dans `store()`, avant `MODELE::create($validated);`, ajoute `$validated['user_id'] = auth()->id();`.
4. Dans `app/Policies/JoueurPolicy.php`, méthodes `update` et `delete` :

```php
return $user->role === 'coach' && $user->id === $joueur->user_id;
```

### 9f. Cacher un bouton dans une vue

```html
@can('update', $joueur)
    <a href="{{ route('joueurs.edit', $joueur) }}">Modifier</a>
@endcan
```

> ⚠️ `@can` **cache seulement** le bouton. La vraie protection, c'est `Gate::authorize` dans le contrôleur (déjà en place).

## 10. Dépannage

| Erreur | Solution |
|---|---|
| `❌ ... n'est pas un projet Laravel` | Le chemin donné au script est faux : il doit pointer vers le dossier qui contient `artisan`. |
| `❌ Dans noms.php, '...' est invalide` | Corrige la ligne indiquée (majuscule, minuscules, pas d'accent). |
| `419 Page Expired` | `@csrf` manquant (il est dans `_form` ; ne l'enlève pas). |
| `Add [x] to fillable property` | Ajoute `'x'` dans `$fillable` du modèle. |
| `no such table` / `no such column` | `php artisan migrate:fresh --seed`. |
| `Attempt to read property "id" on null` (au seed) | Le seeder de l'ENFANT tourne avant celui du PARENT : vérifie l'ordre dans `DatabaseSeeder`. |
| `Undefined variable $coachs` | La variable n'est pas envoyée dans `compact(...)` (pense à `create()` **et** `edit()`). |
| `Route [login] not defined` | Le projet n'a pas de connexion : n'utilise pas `--roles`, ou installe un starter kit. |
| `Target class [IsCoach] does not exist` | L'alias manque dans `bootstrap/app.php` (§9d). |
| `/joueurs/create` renvoie 404 en mode rôles | Le groupe « modifier » doit être **avant** le groupe « consulter » dans `web.php`. |
| Une route modifiée n'est pas prise en compte | `php artisan optimize:clear` |
| Page 500 sans détail | `APP_DEBUG=true` dans `.env`, puis lis `storage/logs/laravel.log`. |

## ☐ Checklist le jour J

- ☐ Projet créé, `php artisan serve` fonctionne
- ☐ PARENT et ENFANT identifiés (« chaque ENFANT a UN PARENT »)
- ☐ `noms.php` rempli (sans accents)
- ☐ `php preparer.php CHEMIN_DU_PROJET` terminé avec « ✅ Terminé »
- ☐ Colonnes en plus ajoutées aux 7 endroits (§8a)
- ☐ `php artisan migrate:fresh --seed` sans erreur
- ☐ `/PARENT_PLURIEL` et `/PLURIEL` affichent les listes
- ☐ Ajout, détail, modification et suppression testés sur les deux
- ☐ Formulaire vide : erreurs rouges
- ☐ Liste déroulante du parent présente dans le formulaire de l'enfant
- ☐ (Rôles) 403 avec `user@example.com`, accès avec le compte `ROLE` et l'admin
