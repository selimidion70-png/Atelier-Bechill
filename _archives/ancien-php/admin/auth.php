<?php
/**
 * auth.php
 * À inclure en tout début de chaque page admin protégée :
 *   session_start();
 *   require_once __DIR__ . '/auth.php';
 *
 * Redirige vers login.php si l'utilisateur n'est pas connecté en tant qu'admin.
 */

if (!isset($_SESSION['operator_id']) || $_SESSION['operator_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
