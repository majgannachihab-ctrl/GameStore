<?php
require_once __DIR__ . '/layout.php';

function afficherListeJeux(array $jeux, string $rechercheAffichee): void
{
    afficherEntete('Liste des jeux');
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Liste des jeux</h2>
        <a href="<?= BASE_URL ?>/index.php?page=jeux&action=ajouter" class="btn btn-success">+ Ajouter un jeu</a>
    </div>

    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-2 mb-4">
        <input type="hidden" name="page" value="jeux">
        <div class="col-auto flex-grow-1">
            <input type="text" name="recherche" class="form-control" placeholder="Rechercher un jeu par titre..."
                   value="<?= htmlspecialchars($rechercheAffichee) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Rechercher</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if (empty($jeux)): ?>
            <p class="text-muted">Aucun jeu trouvé.</p>
        <?php endif; ?>

        <?php foreach ($jeux as $jeu): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($jeu['image'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($jeu['image']) ?>" class="card-img-top" style="height:180px; object-fit:cover;" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                    <?php else: ?>
                        <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center" style="height:180px;">
                            <span class="text-muted">Pas d'image</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($jeu['titre']) ?></h5>
                        <p class="card-text text-muted mb-1"><?= htmlspecialchars($jeu['categorie_nom'] ?? 'Sans catégorie') ?></p>
                        <p class="card-text fw-bold"><?= number_format((float)$jeu['prix'], 2) ?> €</p>
                    </div>
                    <div class="card-footer bg-white d-flex justify-content-between">
                        <button class="btn btn-sm btn-outline-info btn-details" data-id="<?= $jeu['id'] ?>">Détails</button>
                        <a href="<?= BASE_URL ?>/index.php?page=jeux&action=modifier&id=<?= $jeu['id'] ?>" class="btn btn-sm btn-outline-warning">Modifier</a>
                        <a href="<?= BASE_URL ?>/index.php?page=jeux&action=supprimer&id=<?= $jeu['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce jeu ?');">Supprimer</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="modal fade" id="modalDetails" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitre">Détails du jeu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalCorps">Chargement...</div>
            </div>
        </div>
    </div>
    <?php
    afficherPied();
}

/**
 * Catalogue public (boutique) : visible par tout le monde.
 * Le bouton "Ajouter au panier" n'agit que si un client est connecté ;
 * sinon un bouton "Se connecter pour acheter" est affiché à la place.
 */
function afficherCatalogue(array $jeux, string $rechercheAffichee): void
{
    $estClientConnecte = !empty($_SESSION['client_id']);

    afficherEntete('Boutique - Gestion des Jeux Vidéo');
    ?>
    <h2 class="mb-4">Notre catalogue de jeux</h2>

    <form method="GET" action="<?= BASE_URL ?>/index.php" class="row g-2 mb-4">
        <input type="hidden" name="page" value="catalogue">
        <div class="col-auto flex-grow-1">
            <input type="text" name="recherche" class="form-control" placeholder="Rechercher un jeu par titre..."
                   value="<?= htmlspecialchars($rechercheAffichee) ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary">Rechercher</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if (empty($jeux)): ?>
            <p class="text-muted">Aucun jeu trouvé.</p>
        <?php endif; ?>

        <?php foreach ($jeux as $jeu): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($jeu['image'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($jeu['image']) ?>" class="card-img-top" style="height:180px; object-fit:cover;" alt="<?= htmlspecialchars($jeu['titre']) ?>">
                    <?php else: ?>
                        <div class="bg-secondary bg-opacity-25 d-flex align-items-center justify-content-center" style="height:180px;">
                            <span class="text-muted">Pas d'image</span>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($jeu['titre']) ?></h5>
                        <p class="card-text text-muted mb-1"><?= htmlspecialchars($jeu['categorie_nom'] ?? 'Sans catégorie') ?></p>
                        <p class="card-text fw-bold"><?= number_format((float)$jeu['prix'], 2) ?> €</p>
                    </div>
                    <div class="card-footer bg-white">
                        <?php if ($estClientConnecte): ?>
                            <a href="<?= BASE_URL ?>/index.php?page=panier&action=ajouter&id=<?= $jeu['id'] ?>" class="btn btn-success w-100">
                                🛒 Ajouter au panier
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>/index.php?page=client-login" class="btn btn-outline-secondary w-100">
                                Se connecter pour acheter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <p class="text-center text-muted small mt-5">
        <a href="<?= BASE_URL ?>/index.php?page=login">Espace administrateur</a>
    </p>
    <?php
    afficherPied();
}

/**
 * Formulaire unique pour Ajouter ET Modifier un jeu.
 * $mode = 'ajouter' ou 'modifier'
 * $jeu = null en mode ajout, tableau du jeu en mode modification
 */
function afficherFormulaireJeu(string $mode, array $categories, ?array $jeu, ?string $erreur): void
{
    $estModification = ($mode === 'modifier');
    $titrePage = $estModification ? 'Modifier le jeu' : 'Ajouter un jeu';
    $actionUrl = BASE_URL . '/index.php?page=jeux&action=' . $mode;

    afficherEntete($titrePage);
    ?>
    <h2 class="mb-4"><?= $titrePage ?></h2>

    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $actionUrl ?>" enctype="multipart/form-data" id="formJeu" novalidate>

        <?php if ($estModification): ?>
            <input type="hidden" name="id" value="<?= $jeu['id'] ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="titre" class="form-control"
                   value="<?= $estModification ? htmlspecialchars($jeu['titre']) : '' ?>" required>
            <div class="invalid-feedback">Le titre est obligatoire.</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?= $estModification ? htmlspecialchars($jeu['description']) : '' ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Prix (€)</label>
                <input type="number" step="0.01" min="0" name="prix" class="form-control"
                       value="<?= $estModification ? htmlspecialchars($jeu['prix']) : '' ?>" required>
                <div class="invalid-feedback">Le prix doit être un nombre positif.</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Date de sortie</label>
                <input type="date" name="date_sortie" class="form-control"
                       value="<?= $estModification ? htmlspecialchars($jeu['date_sortie'] ?? '') : '' ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Catégorie</label>
            <select name="categorie_id" class="form-select" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= ($estModification && $cat['id'] == $jeu['categorie_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">Veuillez choisir une catégorie.</div>
        </div>

        <?php if ($estModification && !empty($jeu['image'])): ?>
            <div class="mb-3">
                <label class="form-label d-block">Image actuelle</label>
                <img src="<?= UPLOAD_URL . htmlspecialchars($jeu['image']) ?>" style="max-height:120px;">
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label"><?= $estModification ? "Changer l'image (optionnel)" : "Image du jeu" ?></label>
            <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        </div>

        <button type="submit" class="btn <?= $estModification ? 'btn-warning' : 'btn-success' ?>">
            <?= $estModification ? 'Mettre à jour' : 'Enregistrer' ?>
        </button>
        <a href="<?= BASE_URL ?>/index.php?page=jeux" class="btn btn-secondary">Annuler</a>
    </form>
    <?php
    afficherPied();
}