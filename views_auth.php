<?php
require_once __DIR__ . '/layout.php';

function afficherPageLogin(?string $erreur, string $dernierLogin = ''): void
{
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion - Gestion des Jeux Vidéo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body.login-page { background: linear-gradient(135deg, #1f2937, #374151); }
            .card { border: none; border-radius: 0.75rem; }
        </style>
    </head>
    <body class="login-page">
    <div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;">
        <div class="card shadow-sm" style="max-width:400px; width:100%;">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">🎮 Connexion Administrateur</h3>

                <?php if (!empty($erreur)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/index.php?page=login" id="formLogin" novalidate>
                    <div class="mb-3">
                        <label for="login" class="form-label">Identifiant</label>
                        <input type="text" class="form-control" id="login" name="login"
                               value="<?= htmlspecialchars($dernierLogin) ?>" required>
                        <div class="invalid-feedback">L'identifiant est obligatoire.</div>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                        <div class="invalid-feedback">Le mot de passe est obligatoire.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Se connecter</button>
                </form>

                <?php if ($dernierLogin !== ''): ?>
                    <p class="text-muted small mt-3 mb-0 text-center">
                        Identifiant mémorisé via cookie : <?= htmlspecialchars($dernierLogin) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(function () {
            function validerChamp($champ) {
                const valeur = $champ.val() ? $champ.val().trim() : '';
                const valide = !($champ.prop('required') && valeur === '');
                $champ.toggleClass('is-invalid', !valide);
                $champ.toggleClass('is-valid', valide && valeur !== '');
                return valide;
            }
            $('#formLogin').on('input change', 'input', function () { validerChamp($(this)); });
            $('#formLogin').on('submit', function (e) {
                let ok = true;
                $(this).find('input[required]').each(function () { if (!validerChamp($(this))) ok = false; });
                if (!ok) e.preventDefault();
            });
        });
    </script>
    </body>
    </html>
    <?php
}