<?php
require_once __DIR__ . '/models.php';
require_once __DIR__ . '/views_auth.php';
require_once __DIR__ . '/views_jeux.php';
require_once __DIR__ . '/views_categories.php';
require_once __DIR__ . '/views_client.php';
require_once __DIR__ . '/views_panier.php';

// =====================================================
// Controller : Authentification (SESSION + COOKIE)
// =====================================================
class AuthController
{
    private Utilisateur $utilisateurModel;

    public function __construct()
    {
        $this->utilisateurModel = new Utilisateur();
    }

    public function afficherLogin(?string $erreur = null): void
    {
        // On relit le cookie "dernier_login" pour pré-remplir le champ identifiant
        $dernierLogin = $_COOKIE['dernier_login'] ?? '';
        afficherPageLogin($erreur, $dernierLogin);
    }

    public function connexion(): void
    {
        $login = trim($_POST['login'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        if ($login === '' || $motDePasse === '') {
            $this->afficherLogin("Veuillez remplir tous les champs.");
            return;
        }

        $utilisateur = $this->utilisateurModel->verifierConnexion($login, $motDePasse);

        if (!$utilisateur) {
            $this->afficherLogin("Identifiants incorrects.");
            return;
        }

        // ---- SESSION : sert à savoir qu'on est connecté sur toutes les pages ----
        $_SESSION['admin_id'] = $utilisateur['id'];
        $_SESSION['admin_login'] = $utilisateur['login'];

        // ---- COOKIE : mémorise l'identifiant pendant 30 jours pour le pré-remplir ----
        setcookie('dernier_login', $login, time() + 30 * 24 * 3600, '/');

        header('Location: ' . BASE_URL . '/index.php?page=jeux');
        exit;
    }

    public function deconnexion(): void
    {
        // On vide la session (déconnexion), mais on garde le cookie "dernier_login"
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=login');
        exit;
    }

    public static function verifierAuthentification(): void
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_URL . '/index.php?page=login');
            exit;
        }
    }
}

// =====================================================
// Controller : Catégories
// =====================================================
class CategorieController
{
    private Categorie $categorieModel;

    public function __construct()
    {
        AuthController::verifierAuthentification();
        $this->categorieModel = new Categorie();
    }

    public function liste(): void
    {
        $categories = $this->categorieModel->getAll();
        afficherListeCategories($categories);
    }

    public function afficherAjout(?string $erreur = null): void
    {
        afficherFormulaireCategorie('ajouter', null, $erreur);
    }

    public function ajouter(): void
    {
        $nom = trim($_POST['nom'] ?? '');

        if ($nom === '') {
            $this->afficherAjout("Le nom de la catégorie est obligatoire.");
            return;
        }

        $this->categorieModel->create($nom);
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }

    public function afficherModification(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $categorie = $this->categorieModel->getById($id);

        if (!$categorie) {
            header('Location: ' . BASE_URL . '/index.php?page=categories');
            exit;
        }
        afficherFormulaireCategorie('modifier', $categorie, null);
    }

    public function modifier(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $nom = trim($_POST['nom'] ?? '');

        if ($nom === '') {
            $categorie = $this->categorieModel->getById($id);
            afficherFormulaireCategorie('modifier', $categorie, "Le nom de la catégorie est obligatoire.");
            return;
        }

        $this->categorieModel->update($id, $nom);
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }

    public function supprimer(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->categorieModel->delete($id);
        header('Location: ' . BASE_URL . '/index.php?page=categories');
        exit;
    }
}

// =====================================================
// Controller : Jeux (SESSION pour l'auth + COOKIE pour la recherche)
// =====================================================
class JeuController
{
    private Jeu $jeuModel;
    private Categorie $categorieModel;

    public function __construct()
    {
        AuthController::verifierAuthentification();
        $this->jeuModel = new Jeu();
        $this->categorieModel = new Categorie();
    }

    public function liste(): void
    {
        $rechercheGet = trim($_GET['recherche'] ?? '');

        // ---- COOKIE : on mémorise la dernière recherche pendant 7 jours ----
        if ($rechercheGet !== '') {
            setcookie('dernier_recherche', $rechercheGet, time() + 7 * 24 * 3600, '/');
        }

        // Le champ affiché reprend la recherche en cours, sinon la dernière mémorisée
        $rechercheAffichee = $rechercheGet !== '' ? $rechercheGet : ($_COOKIE['dernier_recherche'] ?? '');

        $jeux = $this->jeuModel->getAll($rechercheGet);
        afficherListeJeux($jeux, $rechercheAffichee);
    }

    public function afficherAjout(?string $erreur = null): void
    {
        $categories = $this->categorieModel->getAll();
        afficherFormulaireJeu('ajouter', $categories, null, $erreur);
    }

    public function ajouter(): void
    {
        $erreur = $this->validerFormulaire($_POST);
        if ($erreur) {
            $this->afficherAjout($erreur);
            return;
        }

        $image = $this->traiterUpload();

        $this->jeuModel->create([
            'titre'        => trim($_POST['titre']),
            'description'  => trim($_POST['description'] ?? ''),
            'prix'         => (float)$_POST['prix'],
            'date_sortie'  => $_POST['date_sortie'] ?: null,
            'image'        => $image,
            'categorie_id' => (int)$_POST['categorie_id'],
        ]);

        header('Location: ' . BASE_URL . '/index.php?page=jeux');
        exit;
    }

    public function afficherModification(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $jeu = $this->jeuModel->getById($id);
        $categories = $this->categorieModel->getAll();

        if (!$jeu) {
            header('Location: ' . BASE_URL . '/index.php?page=jeux');
            exit;
        }
        afficherFormulaireJeu('modifier', $categories, $jeu, null);
    }

    public function modifier(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $erreur = $this->validerFormulaire($_POST);
        $categories = $this->categorieModel->getAll();

        if ($erreur) {
            $jeu = $this->jeuModel->getById($id);
            afficherFormulaireJeu('modifier', $categories, $jeu, $erreur);
            return;
        }

        $image = $this->traiterUpload();

        $this->jeuModel->update($id, [
            'titre'        => trim($_POST['titre']),
            'description'  => trim($_POST['description'] ?? ''),
            'prix'         => (float)$_POST['prix'],
            'date_sortie'  => $_POST['date_sortie'] ?: null,
            'image'        => $image,
            'categorie_id' => (int)$_POST['categorie_id'],
        ]);

        header('Location: ' . BASE_URL . '/index.php?page=jeux');
        exit;
    }

    public function supprimer(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $this->jeuModel->delete($id);
        header('Location: ' . BASE_URL . '/index.php?page=jeux');
        exit;
    }

    public function details(): void
    {
        header('Content-Type: application/json');
        $id = (int)($_GET['id'] ?? 0);
        $jeu = $this->jeuModel->getById($id);

        if (!$jeu) {
            http_response_code(404);
            echo json_encode(['erreur' => 'Jeu introuvable']);
            exit;
        }

        echo json_encode($jeu);
        exit;
    }

    private function validerFormulaire(array $data): ?string
    {
        if (trim($data['titre'] ?? '') === '') {
            return "Le titre est obligatoire.";
        }
        if (!isset($data['prix']) || !is_numeric($data['prix']) || (float)$data['prix'] < 0) {
            return "Le prix doit être un nombre positif.";
        }
        if (empty($data['categorie_id'])) {
            return "Veuillez sélectionner une catégorie.";
        }
        return null;
    }

    private function traiterUpload(): ?string
    {
        if (empty($_FILES['image']['name'])) {
            return null;
        }

        $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $extensionsAutorisees, true)) {
            return null;
        }

        $nomFichier = uniqid('jeu_', true) . '.' . $extension;
        $cheminDestination = UPLOAD_DIR . $nomFichier;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $cheminDestination)) {
            return $nomFichier;
        }
        return null;
    }
}

// =====================================================
// Controller : Client (inscription / connexion des visiteurs)
// SESSION -> sait si le client est connecté (accès au panier)
// COOKIE  -> mémorise l'email pour préremplir le prochain login
// =====================================================
class ClientController
{
    private Client $clientModel;

    public function __construct()
    {
        $this->clientModel = new Client();
    }

    public function afficherLogin(?string $erreur = null): void
    {
        $dernierEmail = $_COOKIE['dernier_email_client'] ?? '';
        afficherPageLoginClient($erreur, $dernierEmail);
    }

    public function connexion(): void
    {
        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        if ($email === '' || $motDePasse === '') {
            $this->afficherLogin("Veuillez remplir tous les champs.");
            return;
        }

        $client = $this->clientModel->verifierConnexion($email, $motDePasse);

        if (!$client) {
            $this->afficherLogin("Email ou mot de passe incorrect.");
            return;
        }

        // ---- SESSION : identifie le client connecté sur toutes les pages ----
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_nom'] = $client['nom'];

        // ---- COOKIE : mémorise l'email 30 jours pour préremplir le prochain login ----
        setcookie('dernier_email_client', $email, time() + 30 * 24 * 3600, '/');

        header('Location: ' . BASE_URL . '/index.php?page=catalogue');
        exit;
    }

    public function afficherInscription(?string $erreur = null, array $valeurs = []): void
    {
        afficherPageInscriptionClient($erreur, $valeurs);
    }

    public function inscrire(): void
    {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';
        $confirmation = $_POST['confirmation'] ?? '';
        $valeurs = ['nom' => $nom, 'email' => $email];

        if ($nom === '' || $email === '' || $motDePasse === '') {
            $this->afficherInscription("Veuillez remplir tous les champs.", $valeurs);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->afficherInscription("Email invalide.", $valeurs);
            return;
        }
        if (strlen($motDePasse) < 6) {
            $this->afficherInscription("Le mot de passe doit contenir au moins 6 caractères.", $valeurs);
            return;
        }
        if ($motDePasse !== $confirmation) {
            $this->afficherInscription("Les mots de passe ne correspondent pas.", $valeurs);
            return;
        }
        if ($this->clientModel->emailExiste($email)) {
            $this->afficherInscription("Un compte existe déjà avec cet email.", $valeurs);
            return;
        }

        $this->clientModel->inscrire($nom, $email, $motDePasse);

        // Connexion automatique après inscription
        $client = $this->clientModel->verifierConnexion($email, $motDePasse);
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_nom'] = $client['nom'];
        setcookie('dernier_email_client', $email, time() + 30 * 24 * 3600, '/');

        header('Location: ' . BASE_URL . '/index.php?page=catalogue');
        exit;
    }

    public function deconnexion(): void
    {
        // On retire uniquement les infos client de la session (le panier cookie reste,
        // il sera restauré automatiquement à la prochaine connexion).
        unset($_SESSION['client_id'], $_SESSION['client_nom']);
        header('Location: ' . BASE_URL . '/index.php?page=catalogue');
        exit;
    }

    public static function verifierConnexionClient(): void
    {
        if (empty($_SESSION['client_id'])) {
            header('Location: ' . BASE_URL . '/index.php?page=client-login');
            exit;
        }
    }
}

// =====================================================
// Controller : Panier (SESSION pour la navigation + COOKIE pour la persistance)
// =====================================================
class PanierController
{
    private Jeu $jeuModel;

    public function __construct()
    {
        $this->jeuModel = new Jeu();
    }

    private function sauvegarderCookiePanier(): void
    {
        // Le panier est aussi sauvegardé en cookie (30 jours) pour survivre
        // à la fermeture du navigateur, en plus de la session active.
        setcookie('panier', json_encode($_SESSION['panier']), time() + 30 * 24 * 3600, '/');
    }

    public function ajouter(): void
    {
        ClientController::verifierConnexionClient();

        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $_SESSION['panier'][$id] = ($_SESSION['panier'][$id] ?? 0) + 1;
            $this->sauvegarderCookiePanier();
        }

        header('Location: ' . BASE_URL . '/index.php?page=catalogue');
        exit;
    }

    public function retirer(): void
    {
        ClientController::verifierConnexionClient();

        $id = (int)($_GET['id'] ?? 0);
        unset($_SESSION['panier'][$id]);
        $this->sauvegarderCookiePanier();

        header('Location: ' . BASE_URL . '/index.php?page=panier');
        exit;
    }

    public function vider(): void
    {
        ClientController::verifierConnexionClient();

        $_SESSION['panier'] = [];
        $this->sauvegarderCookiePanier();

        header('Location: ' . BASE_URL . '/index.php?page=panier');
        exit;
    }

    public function afficher(): void
    {
        ClientController::verifierConnexionClient();

        $lignes = [];
        $total = 0.0;

        foreach ($_SESSION['panier'] as $jeuId => $quantite) {
            $jeu = $this->jeuModel->getById((int)$jeuId);
            if ($jeu) {
                $sousTotal = (float)$jeu['prix'] * $quantite;
                $total += $sousTotal;
                $lignes[] = ['jeu' => $jeu, 'quantite' => $quantite, 'sous_total' => $sousTotal];
            }
        }

        afficherPanier($lignes, $total);
    }

    public function confirmer(): void
    {
        ClientController::verifierConnexionClient();

        if (empty($_SESSION['panier'])) {
            header('Location: ' . BASE_URL . '/index.php?page=panier');
            exit;
        }

        $lignes = [];
        $total = 0.0;

        foreach ($_SESSION['panier'] as $jeuId => $quantite) {
            $jeu = $this->jeuModel->getById((int)$jeuId);
            if ($jeu) {
                $total += (float)$jeu['prix'] * $quantite;
                $lignes[] = [
                    'jeu_id'        => $jeu['id'],
                    'quantite'      => $quantite,
                    'prix_unitaire' => $jeu['prix'],
                ];
            }
        }

        $commandeModel = new Commande();
        $commandeId = $commandeModel->creer($_SESSION['client_id'], $lignes, $total);

        // Panier vidé (session ET cookie) après confirmation
        $_SESSION['panier'] = [];
        setcookie('panier', '', time() - 3600, '/');

        afficherConfirmationCommande($commandeId, $total);
    }
}