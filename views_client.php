<?php
require_once __DIR__ . '/layout.php';

function afficherPageLoginClient(?string $erreur, string $dernierEmail = ''): void
{
    afficherEntete('Connexion - Espace client');
    ?>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Connexion</h3>

                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/index.php?page=client-login" id="formLogin" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($dernierEmail) ?>" required>
                            <div class="invalid-feedback">L'email est obligatoire.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" name="mot_de_passe" required>
                            <div class="invalid-feedback">Le mot de passe est obligatoire.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Pas encore de compte ?
                        <a href="<?= BASE_URL ?>/index.php?page=client-inscription">Inscris-toi</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
    afficherPied();
}

function afficherPageInscriptionClient(?string $erreur, array $valeurs = []): void
{
    afficherEntete('Inscription - Espace client');
    ?>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">Créer un compte</h3>

                    <?php if (!empty($erreur)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>/index.php?page=client-inscription" id="formInscription" novalidate>
                        <div class="mb-3">
                            <label class="form-label">Nom complet</label>
                            <input type="text" class="form-control" name="nom"
                                   value="<?= htmlspecialchars($valeurs['nom'] ?? '') ?>" required>
                            <div class="invalid-feedback">Le nom est obligatoire.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                   value="<?= htmlspecialchars($valeurs['email'] ?? '') ?>" required>
                            <div class="invalid-feedback">L'email est obligatoire.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" name="mot_de_passe" minlength="6" required>
                            <div class="invalid-feedback">6 caractères minimum.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmer le mot de passe</label>
                            <input type="password" class="form-control" name="confirmation" minlength="6" required>
                            <div class="invalid-feedback">Les mots de passe doivent correspondre.</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">S'inscrire</button>
                    </form>

                    <p class="text-center mt-3 mb-0">
                        Déjà un compte ?
                        <a href="<?= BASE_URL ?>/index.php?page=client-login">Connecte-toi</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <?php
    afficherPied();
}