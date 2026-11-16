<?php

// ============================================================
//  REMPLIS CE FICHIER, puis lance :  php preparer.php CHEMIN_DU_PROJET
//  (ajoute --roles à la fin pour la partie connexion / rôles)
// ============================================================
//  PARENT = celui qui "a plusieurs"     (ex. Coach, Client, Categorie)
//  ENFANT = celui qui "appartient à"    (ex. Joueur, Commande, Produit)
// ============================================================

return [
    // ---------- PARENT ----------
    'PARENT_MODELE'    => 'Coach',    // singulier, Majuscule au début
    'PARENT_PLURIEL'   => 'coachs',   // pluriel, minuscules (nom de la table)
    'PARENT_SINGULIER' => 'coach',    // singulier, minuscules
    'PARENT_CHAMP'     => 'nom',      // colonne texte du parent

    // ---------- ENFANT ----------
    'MODELE'           => 'Joueur',   // singulier, Majuscule au début
    'PLURIEL'          => 'joueurs',  // pluriel, minuscules (nom de la table)
    'SINGULIER'        => 'joueur',   // singulier, minuscules
    'CHAMP1'           => 'nom',      // colonne texte de l'enfant
    'CHAMP2'           => 'numero',   // colonne nombre de l'enfant

    // ---------- RÔLES (utilisé seulement avec --roles) ----------
    'NOM_MIDDLEWARE'   => 'IsCoach',  // nom de la classe du middleware
    'ROLE'             => 'coach',    // rôle qui a le droit d'ajouter/modifier/supprimer
];
