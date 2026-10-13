<?php
require_once __DIR__ . '/config.php';

// =====================================================
// Model : Utilisateur (authentification)
// =====================================================
class Utilisateur
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    public function verifierConnexion(string $login, string $motDePasse): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM utilisateurs WHERE login = :login LIMIT 1');
        $stmt->execute(['login' => $login]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return $utilisateur;
        }
        return null;
    }
}

// =====================================================
// Model : Categorie (CRUD)
// =====================================================
class Categorie
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    public function getAll(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY nom ASC')->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(string $nom): bool
    {
        $stmt = $this->db->prepare('INSERT INTO categories (nom) VALUES (:nom)');
        return $stmt->execute(['nom' => $nom]);
    }

    public function update(int $id, string $nom): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET nom = :nom WHERE id = :id');
        return $stmt->execute(['nom' => $nom, 'id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}

// =====================================================
// Model : Jeu (CRUD + recherche + JOIN catégories)
// =====================================================
class Jeu
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    public function getAll(string $recherche = ''): array
    {
        $sql = 'SELECT j.*, c.nom AS categorie_nom
                FROM jeux j
                LEFT JOIN categories c ON j.categorie_id = c.id';

        $params = [];
        if ($recherche !== '') {
            $sql .= ' WHERE j.titre LIKE :recherche';
            $params['recherche'] = '%' . $recherche . '%';
        }
        $sql .= ' ORDER BY j.id DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT j.*, c.nom AS categorie_nom
             FROM jeux j
             LEFT JOIN categories c ON j.categorie_id = c.id
             WHERE j.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO jeux (titre, description, prix, date_sortie, image, categorie_id)
             VALUES (:titre, :description, :prix, :date_sortie, :image, :categorie_id)'
        );
        return $stmt->execute([
            'titre'        => $data['titre'],
            'description'  => $data['description'],
            'prix'         => $data['prix'],
            'date_sortie'  => $data['date_sortie'],
            'image'        => $data['image'],
            'categorie_id' => $data['categorie_id'],
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE jeux SET titre = :titre, description = :description,
                prix = :prix, date_sortie = :date_sortie, categorie_id = :categorie_id';
        $params = [
            'titre'        => $data['titre'],
            'description'  => $data['description'],
            'prix'         => $data['prix'],
            'date_sortie'  => $data['date_sortie'],
            'categorie_id' => $data['categorie_id'],
            'id'           => $id,
        ];

        if (!empty($data['image'])) {
            $sql .= ', image = :image';
            $params['image'] = $data['image'];
        }

        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM jeux WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
}

// =====================================================
// Model : Client (inscription + connexion des visiteurs)
// =====================================================
class Client
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM clients WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return (bool)$stmt->fetch();
    }

    public function inscrire(string $nom, string $email, string $motDePasse): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO clients (nom, email, mot_de_passe) VALUES (:nom, :email, :mdp)'
        );
        return $stmt->execute([
            'nom'   => $nom,
            'email' => $email,
            'mdp'   => password_hash($motDePasse, PASSWORD_BCRYPT),
        ]);
    }

    public function verifierConnexion(string $email, string $motDePasse): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM clients WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $client = $stmt->fetch();

        if ($client && password_verify($motDePasse, $client['mot_de_passe'])) {
            return $client;
        }
        return null;
    }
}

// =====================================================
// Model : Commande (validation du panier -> commande + lignes)
// =====================================================
class Commande
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnexion();
    }

    /**
     * Crée une commande et ses lignes à partir du panier.
     * $lignes = [ ['jeu_id' => int, 'quantite' => int, 'prix_unitaire' => float], ... ]
     */
    public function creer(int $clientId, array $lignes, float $total): int
    {
        $this->db->beginTransaction();

        $stmt = $this->db->prepare(
            'INSERT INTO commandes (client_id, total) VALUES (:client_id, :total)'
        );
        $stmt->execute(['client_id' => $clientId, 'total' => $total]);
        $commandeId = (int)$this->db->lastInsertId();

        $stmtLigne = $this->db->prepare(
            'INSERT INTO commande_lignes (commande_id, jeu_id, quantite, prix_unitaire)
             VALUES (:commande_id, :jeu_id, :quantite, :prix_unitaire)'
        );
        foreach ($lignes as $ligne) {
            $stmtLigne->execute([
                'commande_id'   => $commandeId,
                'jeu_id'        => $ligne['jeu_id'],
                'quantite'      => $ligne['quantite'],
                'prix_unitaire' => $ligne['prix_unitaire'],
            ]);
        }

        $this->db->commit();
        return $commandeId;
    }
}