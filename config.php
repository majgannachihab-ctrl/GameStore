<?php
// =====================================================
// Configuration générale : session, base de données, constantes
// =====================================================

session_start();

define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_jeux');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_PATH', __DIR__);
define('BASE_URL', '/GestionJeux'); // adapte au nom exact de ton dossier dans www/
define('UPLOAD_DIR', BASE_PATH . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Connexion PDO unique (singleton via variable statique).
 */
function getConnexion(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . $e->getMessage());
        }
    }

    return $pdo;
}

/**
 * Synchronise le panier entre la SESSION (source de vérité pendant la navigation)
 * et le COOKIE (sauvegarde qui survit à la fermeture du navigateur / expiration de session).
 * Appelée une seule fois, automatiquement, sur chaque page.
 */
function synchroniserPanier(): void
{
    if (!isset($_SESSION['panier'])) {
        if (!empty($_COOKIE['panier'])) {
            $donnees = json_decode($_COOKIE['panier'], true);
            $_SESSION['panier'] = is_array($donnees) ? $donnees : [];
        } else {
            $_SESSION['panier'] = [];
        }
    }
}
synchroniserPanier();

/**
 * Nombre total d'articles dans le panier (toutes quantités confondues),
 * utilisé pour afficher le badge "Mon panier (n)" dans la navbar.
 */
function nombreArticlesPanier(): int
{
    return array_sum($_SESSION['panier'] ?? []);
}