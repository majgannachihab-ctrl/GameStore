<?php
require_once __DIR__ . '/layout.php';

function afficherListeCategories(array $categories): void
{
    afficherEntete('Catégories');
    ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Catégories</h2>
        <a href="<?= BASE_URL ?>/index.php?page=categories&action=ajouter" class="btn btn-success">+ Ajouter une catégorie</a>
    </div>

    <table class="table table-bordered table-hover bg-white">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th style="width:220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr><td colspan="3" class="text-center text-muted">Aucune catégorie enregistrée.</td></tr>
            <?php endif; ?>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['nom']) ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/index.php?page=categories&action=modifier&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-warning">Modifier</a>
                        <a href="<?= BASE_URL ?>/index.php?page=categories&action=supprimer&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette catégorie ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    afficherPied();
}

/**
 * Formulaire unique pour Ajouter ET Modifier une catégorie.
 * $mode = 'ajouter' ou 'modifier'
 */
function afficherFormulaireCategorie(string $mode, ?array $categorie, ?string $erreur): void
{
    $estModification = ($mode === 'modifier');
    $titrePage = $estModification ? 'Modifier la catégorie' : 'Ajouter une catégorie';
    $actionUrl = BASE_URL . '/index.php?page=categories&action=' . $mode;

    afficherEntete($titrePage);
    ?>
    <h2 class="mb-4"><?= $titrePage ?></h2>

    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $actionUrl ?>" id="formCategorie" novalidate>
        <?php if ($estModification): ?>
            <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Nom de la catégorie</label>
            <input type="text" name="nom" class="form-control"
                   value="<?= $estModification ? htmlspecialchars($categorie['nom']) : '' ?>" required>
            <div class="invalid-feedback">Le nom est obligatoire.</div>
        </div>
        <button type="submit" class="btn <?= $estModification ? 'btn-warning' : 'btn-success' ?>">
            <?= $estModification ? 'Mettre à jour' : 'Enregistrer' ?>
        </button>
        <a href="<?= BASE_URL ?>/index.php?page=categories" class="btn btn-secondary">Annuler</a>
    </form>
    <?php
    afficherPied();
}