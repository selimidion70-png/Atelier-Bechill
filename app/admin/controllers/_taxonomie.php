<?php
// Controller partagé par /admin/categories, /admin/themes et /admin/tags.
// Variable attendue : $type (clé de TAXONOMIES), définie par le controller qui inclut ce fichier.
require_once __DIR__ . '/../../models/taxonomie.php';

$tx     = TAXONOMIES[$type];
$url    = '/admin/' . $type;
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);
    $nom    = trim($_POST['nom'] ?? '');

    if ($action === 'ajouter' || $action === 'renommer') {
        if ($nom === '' || mb_strlen($nom) > 100) {
            $erreur = "Le nom est obligatoire (100 caractères maximum).";
        } elseif (taxonomie_nom_existe($pdo, $tx, $nom, $action === 'renommer' ? $id : 0)) {
            $erreur = "« $nom » existe déjà.";
        } elseif ($action === 'ajouter') {
            taxonomie_insert($pdo, $tx, $nom);
        } else {
            taxonomie_rename($pdo, $tx, $id, $nom);
        }
    } elseif ($action === 'supprimer') {
        $element = taxonomie_get($pdo, $tx, $id);
        if ($element && $tx['bloquante'] && $element['nb_soins'] > 0) {
            $erreur = "Impossible de supprimer « {$element['nom']} » : {$element['nb_soins']} soin(s) l'utilisent encore. Changez d'abord la {$tx['singulier']} de ces soins.";
        } elseif ($element) {
            taxonomie_delete($pdo, $tx, $id);
        }
    }

    if ($erreur === '') {
        redirect($url);
    }
}

echo render($base . '/views/taxonomie.php', [
    'tx'        => $tx,
    'url'       => $url,
    'erreur'    => $erreur,
    'elements'  => taxonomie_get_all($pdo, $tx),
    'pageTitle' => $tx['titre'] . ' – Admin',
]);
