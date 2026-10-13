<?php
require_once __DIR__ . '/layout.php';

/**
 * $lignes = [ ['jeu' => array, 'quantite' => int, 'sous_total' => float], ... ]
 */
function afficherPanier(array $lignes, float $total): void
{
    afficherEntete('Mon panier');
    ?>
    <h2 class="mb-4">Mon panier</h2>

    <?php if (empty($lignes)): ?>
        <div class="alert alert-info">
            Ton panier est vide pour le moment.
            <a href="<?= BASE_URL ?>/index.php?page=catalogue">Retourner au catalogue</a>
        </div>
    <?php else: ?>
        <table class="table table-bordered bg-white align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Jeu</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Sous-total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lignes as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['jeu']['titre']) ?></td>
                        <td><?= number_format((float)$ligne['jeu']['prix'], 2) ?> €</td>
                        <td><?= (int)$ligne['quantite'] ?></td>
                        <td><?= number_format((float)$ligne['sous_total'], 2) ?> €</td>
                        <td>
                            <a href="<?= BASE_URL ?>/index.php?page=panier&action=retirer&id=<?= $ligne['jeu']['id'] ?>"
                               class="btn btn-sm btn-outline-danger">Retirer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th colspan="2"><?= number_format($total, 2) ?> €</th>
                </tr>
            </tfoot>
        </table>

        <div class="d-flex justify-content-between">
            <a href="<?= BASE_URL ?>/index.php?page=panier&action=vider" class="btn btn-outline-secondary"
               onclick="return confirm('Vider tout le panier ?');">Vider le panier</a>

            <a href="<?= BASE_URL ?>/index.php?page=panier&action=confirmer" class="btn btn-success btn-lg"
               onclick="return confirm('Confirmer la commande pour un total de <?= number_format($total, 2) ?> € ?');">
                ✅ Confirmer la commande
            </a>
        </div>
    <?php endif; ?>
    <?php
    afficherPied();
}

function afficherConfirmationCommande(int $commandeId, float $total): void
{
    afficherEntete('Commande confirmée');
    ?>
    <div class="text-center py-5">
        <h2 class="text-success mb-3">✅ Merci pour ta commande !</h2>
        <p class="fs-5">Commande n°<strong>#<?= $commandeId ?></strong> confirmée pour un total de
            <strong><?= number_format($total, 2) ?> €</strong>.</p>
        <a href="<?= BASE_URL ?>/index.php?page=catalogue" class="btn btn-primary mt-3">Retourner au catalogue</a>
    </div>
    <?php
    afficherPied();
}