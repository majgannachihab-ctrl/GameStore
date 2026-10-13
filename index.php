<?php
require_once __DIR__ . '/controllers.php';

$page = $_GET['page'] ?? 'catalogue';
$action = $_GET['action'] ?? null;
$methode = $_SERVER['REQUEST_METHOD'];

switch ($page) {

    // ---------- BOUTIQUE PUBLIQUE ----------
    case 'catalogue':
        $jeuModel = new Jeu();
        $recherche = trim($_GET['recherche'] ?? '');
        $jeux = $jeuModel->getAll($recherche);
        afficherCatalogue($jeux, $recherche);
        break;

    // ---------- ESPACE CLIENT ----------
    case 'client-login':
        $controller = new ClientController();
        if ($methode === 'POST') {
            $controller->connexion();
        } else {
            $controller->afficherLogin();
        }
        break;

    case 'client-inscription':
        $controller = new ClientController();
        if ($methode === 'POST') {
            $controller->inscrire();
        } else {
            $controller->afficherInscription();
        }
        break;

    case 'client-logout':
        (new ClientController())->deconnexion();
        break;

    // ---------- PANIER ----------
    case 'panier':
        $controller = new PanierController();
        if ($action === 'ajouter') {
            $controller->ajouter();
        } elseif ($action === 'retirer') {
            $controller->retirer();
        } elseif ($action === 'vider') {
            $controller->vider();
        } elseif ($action === 'confirmer') {
            $controller->confirmer();
        } else {
            $controller->afficher();
        }
        break;

    // ---------- AUTHENTIFICATION ADMIN ----------
    case 'login':
        $controller = new AuthController();
        if ($methode === 'POST') {
            $controller->connexion();
        } else {
            $controller->afficherLogin();
        }
        break;

    case 'logout':
        (new AuthController())->deconnexion();
        break;

    // ---------- JEUX (ADMIN) ----------
    case 'jeux':
        $controller = new JeuController();
        if ($action === 'ajouter' && $methode === 'GET') {
            $controller->afficherAjout();
        } elseif ($action === 'ajouter' && $methode === 'POST') {
            $controller->ajouter();
        } elseif ($action === 'modifier' && $methode === 'GET') {
            $controller->afficherModification();
        } elseif ($action === 'modifier' && $methode === 'POST') {
            $controller->modifier();
        } elseif ($action === 'supprimer') {
            $controller->supprimer();
        } elseif ($action === 'details') {
            $controller->details(); // réponse JSON (AJAX)
        } else {
            $controller->liste();
        }
        break;

    // ---------- CATEGORIES (ADMIN) ----------
    case 'categories':
        $controller = new CategorieController();
        if ($action === 'ajouter' && $methode === 'GET') {
            $controller->afficherAjout();
        } elseif ($action === 'ajouter' && $methode === 'POST') {
            $controller->ajouter();
        } elseif ($action === 'modifier' && $methode === 'GET') {
            $controller->afficherModification();
        } elseif ($action === 'modifier' && $methode === 'POST') {
            $controller->modifier();
        } elseif ($action === 'supprimer') {
            $controller->supprimer();
        } else {
            $controller->liste();
        }
        break;

    default:
        header('Location: ' . BASE_URL . '/index.php?page=catalogue');
        exit;
}