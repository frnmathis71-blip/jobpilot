# CONTEXT.md — Architecture de JobPilot

Ce document sert de carte du projet. Il décrit l'état du code tel qu'il est,
y compris ses incohérences (voir la dernière section).

## 1. Stack technique

| Couche | Outil |
|---|---|
| Langage | PHP ^8.3 (attention : le `composer.lock` actuel tire Symfony 8.1, qui exige PHP ≥ 8.4.1) |
| Framework | Laravel 13 (base : *Laravel Livewire Starter Kit*) |
| Authentification | Laravel Fortify (connexion, inscription, vérification e-mail, 2FA, passkeys, reset mot de passe) |
| UI | Blade + Livewire 4 + Flux UI (pages auth et paramètres) ; les vues CRUD (`offer/`, `company/`, `candidat/`) sont en HTML brut, sans layout |
| Front | Vite + Tailwind (`resources/css/app.css`, `resources/js/app.js`) |
| Base de données | SQLite par défaut (`.env.example`), SQLite en mémoire pour les tests |
| Tests | PHPUnit 12 (`tests/Feature`, `tests/Unit`) |
| Qualité | Pint, Larastan (`phpstan.neon`), CI GitHub Actions (`.github/workflows/tests.yml`) |

Il n'y a **pas de front séparé** (pas de React ni de Vue) : le « routeur front »,
c'est `routes/web.php`, et les conditions d'affichage se trouvent dans les vues Blade.

## 2. Structure des dossiers

```
app/
  Actions/Fortify/        CreateNewUser (inscription + rôle), ResetUserPassword
  Concerns/               Règles de validation partagées (mot de passe, profil)
  Http/
    Controllers/          OfferController, CompanyController, CandidatController (resources CRUD)
    Middleware/           IsAdmin, IsCompany, IsCandidat (contrôle du rôle)
    Requests/             Store/Update*Request (authorize() = true, rules() vides)
  Livewire/Actions/       Logout
  Models/                 User, Company, Candidat, Offer
  Policies/               OfferPolicy, CompanyPolicy, CandidatPolicy (squelettes, non utilisées)
  Providers/              AppServiceProvider, FortifyServiceProvider
bootstrap/app.php         Routage + alias des middlewares de rôle
config/fortify.php        Fonctionnalités Fortify activées, home = /dashboard
database/
  migrations/             users, companies, offers, candidats, cache, jobs, passkeys, 2FA
  factories/ seeders/
resources/views/
  offer/ company/ candidat/   Vues CRUD (index, create, edit, show)
  pages/auth/                 Vues Fortify (login, register…)
  pages/settings/             Composants Livewire (profil, sécurité, apparence)
  layouts/                    Layouts app/auth (Flux)
routes/
  web.php                 Routes métier (offres, entreprises, candidats)
  settings.php            Routes des paramètres utilisateur
tests/Feature/            Tests auth, settings, dashboard, accès aux offres
```

## 3. Authentification et rôles

### Flux d'authentification
1. **Inscription** (`/register`, vue `pages/auth/register.blade.php`) : l'utilisateur
   choisit le rôle `candidat` ou `company` (bouton radio `role`).
2. `App\Actions\Fortify\CreateNewUser` valide `role ∈ {candidat, company}`, puis, dans une transaction :
   - pour `company`, crée un `Company` vide et renseigne `users.company_id` ;
   - pour `candidat`, crée un `Candidat` vide et renseigne `users.candidat_id` ;
   - crée le `User` avec la colonne `role`.
3. **Connexion** gérée par Fortify (session, guard `web`), puis redirection vers `/dashboard`.
4. `User` implémente `MustVerifyEmail` : les routes protégées par `verified` exigent un e-mail
   vérifié. Les routes métier (`web.php`) utilisent seulement `auth`, pas `verified`.

### Stockage et lecture du rôle
- Colonne `users.role` : `enum('candidat','company','admin')`, valeur par défaut `candidat`.
- `role` est dans `#[Fillable]` du modèle `User`.
- Il n'y a pas de token JWT ni d'API : le rôle est relu en base à chaque requête via
  `auth()->user()->role` (session Laravel).
- Le rôle `admin` ne peut **pas** être choisi à l'inscription : il faut le mettre à la main en base.

### Middlewares de rôle (`bootstrap/app.php` → alias)
| Alias | Classe | Condition |
|---|---|---|
| `IsAdmin` | `App\Http\Middleware\IsAdmin` | `role === 'admin'`, sinon 403 |
| `IsCompany` | `App\Http\Middleware\IsCompany` | `role === 'company'`, sinon 403 |
| `IsCandidat` | `App\Http\Middleware\IsCandidat` | `role === 'candidat'`, sinon 403 (non utilisé) |

Les **Policies** existent mais renvoient toutes `false` et ne sont appelées nulle part
(pas de `authorize()` ni de `can`). Les **FormRequests** renvoient `authorize() = true`.

## 4. Routes principales

> ⚠️ Laravel retient la **première** route enregistrée qui correspond à l'URL.
> Les routes statiques (`/offer/create`) doivent être déclarées avant les routes
> paramétrées (`/offer/{offer}`). C'est la cause du bug corrigé sur `/offer/create`.

| Méthode | URL | Nom | Middleware | Rôles autorisés |
|---|---|---|---|---|
| GET | `/` | home | — | tous |
| GET | `/dashboard` | dashboard | — (aucun `auth` !) | tous |
| GET | `/offer/create` | offer.create | auth, IsCompany | company |
| POST | `/offer` | offer.store | auth, IsCompany | company |
| GET | `/offer/{offer}/edit` | offer.edit | auth, IsCompany | company |
| PUT/PATCH | `/offer/{offer}` | offer.update | auth, IsCompany | company |
| DELETE | `/offer/{offer}` | offer.destroy | auth, IsAdmin | admin |
| GET | `/offer` | offer.index | auth | connectés |
| GET | `/offer/{offer}` | offer.show | auth | connectés |
| * | `/company/*` (CRUD complet) | company.* | auth, IsAdmin | admin |
| GET/POST/PUT/DELETE | `/candidat/create`, `store`, `edit`, `update`, `destroy` | candidat.* | auth, IsAdmin | admin |
| GET | `/candidat`, `/candidat/{candidat}` | candidat.index/show | auth | connectés |
| GET | `/settings/profile` | profile.edit | auth | connectés |
| GET | `/settings/appearance`, `/settings/security` | … | auth, verified (+ password.confirm) | connectés vérifiés |
| — | `/login`, `/register`, `/logout`, `/forgot-password`… | Fortify | — | — |

Pour vérifier : `php artisan route:list --path=offer`.

## 5. Modèles de données

```
User ──belongsTo──> Company   (users.company_id, nullable)
User ──belongsTo──> Candidat  (users.candidat_id, nullable)
Company ──hasMany──> Offer    (offers.company_id)
Company ──hasOne──> User
Candidat ──hasOne──> User
Candidat ──hasMany──> Offer   (⚠ aucune colonne candidat_id dans offers)
candidats.offer_id ──> offers (nullable)
```

| Modèle | Table / colonnes (migrations) | `$fillable` |
|---|---|---|
| User | id, name, company_id, candidat_id, email, email_verified_at, password, role, remember_token, colonnes 2FA | name, email, password, role, candidat_id, company_id |
| Company | id, timestamps (**pas de `name`**) | name |
| Candidat | id, timestamps, offer_id (**pas de `fullname`**) | fullname |
| Offer | id, timestamps, company_id, Salary (int), Name, Description | Name, Salary, company_id, Description |

## 6. Points d'attention et incohérences repérés

1. **[Corrigé] Ordre des routes dans `routes/web.php`** : le groupe « consultation »
   (`index`, `show`) était déclaré en premier, donc `GET /offer/create` correspondait à
   `offer.show` avec `{offer} = "create"`, ce qui donnait un 404 (model binding). Même problème
   pour `GET /candidat/create` (admin). Le groupe entreprise passe maintenant en premier.
2. **Ordre des migrations** : `0001_01_01_000000_create_users_table` déclare des clés étrangères vers
   `companies` et `candidats`, tables créées par des migrations datées de 2026. SQLite le tolère,
   mais `migrate` échouera sur MySQL ou PostgreSQL.
3. **Colonnes manquantes** : `companies.name` et `candidats.fullname` n'existent pas, alors que les
   modèles, seeders, contrôleurs et vues les utilisent (`CompanySeeder` plantera, la liste
   déroulante des entreprises dans `offer/create` sera vide de noms).
4. **Une entreprise peut publier ou modifier l'offre d'une autre entreprise** : `offer/create` propose
   toutes les entreprises (`Company::all()`) et `edit`/`update` ne vérifient pas l'appartenance.
   Il faudrait forcer `company_id = auth()->user()->company_id` et brancher `OfferPolicy`.
5. **Vue `offer/index`** : pour le rôle `company`, elle affiche le bouton « Supprimer » (route
   admin, donc 403). Pour le rôle `admin`, elle affiche « Ajouter une offre » et « Modifier »
   (routes company, donc 403).
6. **Vue `offer/show`** : elle n'affiche rien pour `admin` et `company` (condition `['candidat']` seule).
7. **Casse des attributs Offer** : les colonnes sont `Name`, `Salary`, `Description` (majuscules),
   mais les vues utilisent `$offer->name` et l'`OfferFactory` utilise `name`/`salary`/`description`.
8. **`/dashboard` n'a pas de middleware `auth`**, alors que le layout appelle
   `auth()->user()->name`, ce qui provoque une erreur pour un visiteur non connecté (le test
   `DashboardTest` attend une redirection vers login).
9. **Policies inutilisées** (toutes à `false`) et **FormRequests vides** : la validation est faite
   en double dans les contrôleurs via `$request->validate()`.
10. `IsCandidat` est déclaré mais n'est utilisé par aucune route.
11. Faute de frappe dans les messages flash : clé `succes` (au lieu de `success`) ; `update` renvoie « ajoutée ».
