<?php
// =====================================================
// Layout partagé : entête + pied de page (avec CSS et JS intégrés)
// =====================================================

function afficherEntete(string $titreOnglet = 'Gestion des Jeux Vidéo'): void
{
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($titreOnglet) ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f4f6f9; }
            .login-page { background: linear-gradient(135deg, #1f2937, #374151); }
            .card { border: none; border-radius: 0.75rem; }
            .navbar-brand { font-weight: bold; }
            .card-img-top { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; }
            .table th { vertical-align: middle; }
        </style>
    </head>
    <body>

    <?php if (!empty($_SESSION['admin_id'])): ?>
    <!-- ---------- Navbar ADMIN ---------- -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/index.php?page=jeux">🎮 GestionJeux (Admin)</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php?page=jeux">Jeux</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php?page=categories">Catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php?page=catalogue">Voir la boutique</a></li>
                </ul>
                <span class="navbar-text me-3 text-light">
                    Connecté : <?= htmlspecialchars($_SESSION['admin_login']) ?>
                </span>
                <a href="<?= BASE_URL ?>/index.php?page=logout" class="btn btn-outline-light btn-sm">Déconnexion</a>
            </div>
        </div>
    </nav>

    <?php elseif (!empty($_SESSION['client_id'])): ?>
    <!-- ---------- Navbar CLIENT connecté ---------- -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/index.php?page=catalogue">🎮 GestionJeux</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php?page=catalogue">Catalogue</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/index.php?page=panier">
                            🛒 Mon panier (<?= nombreArticlesPanier() ?>)
                        </a>
                    </li>
                </ul>
                <span class="navbar-text me-3 text-light">
                    Bonjour, <?= htmlspecialchars($_SESSION['client_nom']) ?>
                </span>
                <a href="<?= BASE_URL ?>/index.php?page=client-logout" class="btn btn-outline-light btn-sm">Déconnexion</a>
            </div>
        </div>
    </nav>

    <?php else: ?>
    <!-- ---------- Navbar VISITEUR (non connecté) ---------- -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>/index.php?page=catalogue">🎮 GestionJeux</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php?page=catalogue">Catalogue</a></li>
                </ul>
                <a href="<?= BASE_URL ?>/index.php?page=client-login" class="btn btn-outline-light btn-sm me-2">Connexion</a>
                <a href="<?= BASE_URL ?>/index.php?page=client-inscription" class="btn btn-light btn-sm">Inscription</a>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <div class="container py-4">
    <?php
}

function afficherPied(): void
{
    ?>
    </div><!-- /container -->

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const BASE_URL_JS = "<?= BASE_URL ?>";

        $(function () {
            // ---------- Validation en temps réel (jQuery) ----------
            function validerChamp($champ) {
                const valeur = $champ.val() ? $champ.val().trim() : '';
                let valide = true;

                if ($champ.prop('required') && valeur === '') {
                    valide = false;
                }
                if ($champ.attr('type') === 'number' && valeur !== '') {
                    const nombre = parseFloat(valeur);
                    if (isNaN(nombre) || nombre < 0) valide = false;
                }

                $champ.toggleClass('is-invalid', !valide);
                $champ.toggleClass('is-valid', valide && valeur !== '');
                return valide;
            }

            $('#formJeu, #formCategorie, #formLogin').on('input change', 'input, select, textarea', function () {
                validerChamp($(this));
            });

            $('#formJeu, #formCategorie, #formLogin').on('submit', function (e) {
                let formValide = true;
                $(this).find('input[required], select[required]').each(function () {
                    if (!validerChamp($(this))) formValide = false;
                });
                if (!formValide) e.preventDefault();
            });

            // ---------- AJAX : détails d'un jeu en JSON, sans recharger la page ----------
            $(document).on('click', '.btn-details', function () {
                const idJeu = $(this).data('id');
                $('#modalCorps').html('Chargement...');
                $('#modalDetails').modal('show');

                $.ajax({
                    url: BASE_URL_JS + '/index.php',
                    method: 'GET',
                    data: { page: 'jeux', action: 'details', id: idJeu },
                    dataType: 'json',
                    success: function (jeu) {
                        if (jeu.erreur) {
                            $('#modalCorps').html('<p class="text-danger">' + jeu.erreur + '</p>');
                            return;
                        }
                        $('#modalTitre').text(jeu.titre);
                        let imageHtml = '';
                        if (jeu.image) {
                            imageHtml = '<img src="' + BASE_URL_JS + '/uploads/' + jeu.image + '" class="img-fluid mb-3 rounded">';
                        }
                        const contenu =
                            imageHtml +
                            '<p><strong>Description :</strong> ' + (jeu.description || 'Aucune description') + '</p>' +
                            '<p><strong>Prix :</strong> ' + parseFloat(jeu.prix).toFixed(2) + ' €</p>' +
                            '<p><strong>Date de sortie :</strong> ' + (jeu.date_sortie || 'Non renseignée') + '</p>' +
                            '<p><strong>Catégorie :</strong> ' + (jeu.categorie_nom || 'Sans catégorie') + '</p>';
                        $('#modalCorps').html(contenu);
                    },
                    error: function () {
                        $('#modalCorps').html('<p class="text-danger">Erreur lors du chargement des détails.</p>');
                    }
                });
            });
        });
    </script>
    </body>
    </html>
    <?php
}