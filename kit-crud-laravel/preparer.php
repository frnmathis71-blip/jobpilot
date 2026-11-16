<?php

// ============================================================
//  Installe le kit CRUD (PARENT + ENFANT) dans un projet Laravel.
//
//  Usage :  php preparer.php CHEMIN_DU_PROJET
//           php preparer.php CHEMIN_DU_PROJET --roles
//
//  1. Remplis d'abord noms.php
//  2. Lance la commande
//  3. Dans le projet : php artisan migrate:fresh --seed
// ============================================================

$kit = __DIR__;
$args = array_slice($argv, 1);
$roles = in_array('--roles', $args, true);
$args = array_values(array_filter($args, fn ($a) => $a !== '--roles'));

function stop(string $message): never
{
    fwrite(STDERR, "\n❌ $message\n\n");
    exit(1);
}

function info(string $message): void
{
    echo $message . PHP_EOL;
}

if (count($args) !== 1) {
    stop("Usage : php preparer.php CHEMIN_DU_PROJET [--roles]");
}

$projet = rtrim($args[0], '/\\');
if (!is_file("$projet/artisan")) {
    stop("« $projet » n'est pas un projet Laravel (fichier artisan introuvable).");
}

// ---------- 1. Lecture et contrôle des noms ----------
$noms = require "$kit/noms.php";

$formats = [
    'PARENT_MODELE'    => ['/^[A-Z][A-Za-z]*$/', 'Majuscule au début, lettres seulement (ex. Coach)'],
    'PARENT_PLURIEL'   => ['/^[a-z][a-z_]*$/', 'minuscules (ex. coachs)'],
    'PARENT_SINGULIER' => ['/^[a-z][a-z_]*$/', 'minuscules (ex. coach)'],
    'PARENT_CHAMP'     => ['/^[a-z][a-z0-9_]*$/', 'minuscules (ex. nom)'],
    'MODELE'           => ['/^[A-Z][A-Za-z]*$/', 'Majuscule au début, lettres seulement (ex. Joueur)'],
    'PLURIEL'          => ['/^[a-z][a-z_]*$/', 'minuscules (ex. joueurs)'],
    'SINGULIER'        => ['/^[a-z][a-z_]*$/', 'minuscules (ex. joueur)'],
    'CHAMP1'           => ['/^[a-z][a-z0-9_]*$/', 'minuscules (ex. nom)'],
    'CHAMP2'           => ['/^[a-z][a-z0-9_]*$/', 'minuscules (ex. numero)'],
    'NOM_MIDDLEWARE'   => ['/^[A-Z][A-Za-z]*$/', 'Majuscule au début (ex. IsCoach)'],
    'ROLE'             => ['/^[a-z][a-z_]*$/', 'minuscules (ex. coach)'],
];

foreach ($formats as $cle => [$regex, $aide]) {
    if (!isset($noms[$cle]) || !preg_match($regex, $noms[$cle])) {
        stop("Dans noms.php, '$cle' est invalide : $aide.");
    }
}
if ($noms['CHAMP1'] === $noms['CHAMP2']) {
    stop("Dans noms.php, CHAMP1 et CHAMP2 doivent être différents.");
}
if ($noms['PLURIEL'] === $noms['PARENT_PLURIEL'] || $noms['MODELE'] === $noms['PARENT_MODELE']) {
    stop("Dans noms.php, le PARENT et l'ENFANT doivent avoir des noms différents.");
}
if ($roles && $noms['ROLE'] === 'admin') {
    stop("Dans noms.php, ROLE ne peut pas être 'admin' (l'admin a déjà tous les droits).");
}

// strtr remplace toujours le mot le plus long en premier :
// PARENT_MODELE est donc remplacé avant MODELE.
$remplacer = fn (string $texte): string => strtr($texte, $noms);

// ---------- 2. Copie des fichiers ----------
function copierDossier(string $source, string $projet, callable $remplacer): void
{
    $fichiers = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($fichiers as $fichier) {
        $relatif = str_replace('\\', '/', substr($fichier->getPathname(), strlen($source) + 1));

        if ($relatif === 'routes/web.php') {
            continue; // les routes sont ajoutées à part (étape 3)
        }

        $cible = $projet . '/' . $remplacer($relatif);
        if (!is_dir(dirname($cible))) {
            mkdir(dirname($cible), 0777, true);
        }
        file_put_contents($cible, $remplacer(file_get_contents($fichier->getPathname())));
        info("  ✔ " . $remplacer($relatif));
    }
}

info("\nCopie des fichiers :");
copierDossier("$kit/fichiers", $projet, $remplacer);
if ($roles) {
    copierDossier("$kit/fichiers-roles", $projet, $remplacer);
}

// ---------- 3. Routes : ajoute (ou remplace) le bloc du kit ----------
$debut = '// >>> KIT CRUD (début)';
$fin = '// <<< KIT CRUD (fin)';

$modele = file_get_contents($kit . ($roles ? '/fichiers-roles' : '/fichiers') . '/routes/web.php');
$bloc = substr($modele, strpos($modele, $debut));
$bloc = $remplacer(substr($bloc, 0, strpos($bloc, $fin) + strlen($fin)));

$webPhp = "$projet/routes/web.php";
$routes = file_get_contents($webPhp);
$posDebut = strpos($routes, $debut);
$posFin = strpos($routes, $fin);

if ($posDebut !== false && $posFin !== false) {
    $routes = substr($routes, 0, $posDebut) . $bloc . substr($routes, $posFin + strlen($fin));
} else {
    $routes = rtrim($routes) . "\n\n" . $bloc . "\n";
}
file_put_contents($webPhp, $routes);
info("  ✔ routes/web.php (bloc KIT CRUD)");

// ---------- 4. Option --roles : modifications de fichiers existants ----------
if ($roles) {
    info("\nOption --roles :");

    // 4a. Colonne role dans la table users
    $migrationsUsers = glob("$projet/database/migrations/*create_users_table.php");
    $ok = false;
    if (count($migrationsUsers) === 1) {
        $contenu = file_get_contents($migrationsUsers[0]);
        if (str_contains($contenu, "'role'")) {
            $ok = true;
        } elseif (str_contains($contenu, "\$table->string('password');")) {
            $contenu = str_replace(
                "\$table->string('password');",
                "\$table->string('password');\n            \$table->string('role')->default('user');",
                $contenu
            );
            file_put_contents($migrationsUsers[0], $contenu);
            $ok = true;
        }
    }
    info($ok
        ? "  ✔ colonne role dans la migration users"
        : "  ⚠ À FAIRE À LA MAIN : dans la migration users, ajoute \$table->string('role')->default('user');");

    // 4b. Alias du middleware dans bootstrap/app.php
    $appPhp = "$projet/bootstrap/app.php";
    $contenu = file_get_contents($appPhp);
    $alias = "'{$noms['NOM_MIDDLEWARE']}' =>";
    $ok = str_contains($contenu, $alias);
    if (!$ok) {
        $ajout = "\n        \$middleware->alias([\n"
            . "            '{$noms['NOM_MIDDLEWARE']}' => \\App\\Http\\Middleware\\{$noms['NOM_MIDDLEWARE']}::class,\n"
            . "        ]);\n";
        $contenu = preg_replace(
            '/(->withMiddleware\(function \(Middleware \$middleware\)(?:\s*:\s*void)?\s*\{)/',
            '$1' . str_replace('\\', '\\\\', $ajout),
            $contenu,
            1,
            $nb
        );
        if ($nb === 1) {
            file_put_contents($appPhp, $contenu);
            $ok = true;
        }
    }
    info($ok
        ? "  ✔ alias {$noms['NOM_MIDDLEWARE']} dans bootstrap/app.php"
        : "  ⚠ À FAIRE À LA MAIN : alias du middleware dans bootstrap/app.php (voir le PDF)");

    // 4c. Gate 'acces-admin' dans AppServiceProvider
    $provider = "$projet/app/Providers/AppServiceProvider.php";
    $contenu = file_get_contents($provider);
    $ok = str_contains($contenu, "'acces-admin'");
    if (!$ok) {
        $ajout = "\n        \\Illuminate\\Support\\Facades\\Gate::define('acces-admin', function (\\App\\Models\\User \$user) {\n"
            . "            return \$user->role === 'admin';\n"
            . "        });\n";
        $contenu = preg_replace(
            '/(public function boot\(\)(?:\s*:\s*void)?\s*\{)/',
            '$1' . str_replace('\\', '\\\\', $ajout),
            $contenu,
            1,
            $nb
        );
        if ($nb === 1) {
            file_put_contents($provider, $contenu);
            $ok = true;
        }
    }
    info($ok
        ? "  ✔ Gate acces-admin dans AppServiceProvider"
        : "  ⚠ À FAIRE À LA MAIN : Gate::define('acces-admin', ...) dans AppServiceProvider (voir le PDF)");

    // 4d. Le projet a-t-il un système de connexion ?
    $composer = file_get_contents("$projet/composer.json");
    if (!str_contains($composer, 'laravel/fortify') && !str_contains($composer, 'laravel/breeze') && !str_contains($composer, 'laravel/jetstream')) {
        info("  ⚠ ATTENTION : aucun système de connexion détecté (Fortify, Breeze...).");
        info("    Sans page /login, les routes protégées afficheront « Route [login] not defined ».");
    }
}

// ---------- 5. Suite ----------
info("\n✅ Terminé. Dans le dossier du projet, tape :");
info("   php artisan migrate:fresh --seed");
info("   php artisan serve");
info("Puis ouvre :");
info("   http://127.0.0.1:8000/{$noms['PARENT_PLURIEL']}");
info("   http://127.0.0.1:8000/{$noms['PLURIEL']}\n");
