<?php
// $articles, $villes, $achats, $message
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Achats</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Titres */
        h2.page-title {
            color: #001f3f;
            font-weight: bold;
        }
        p.page-subtitle {
            color: #3a5f9e;
            font-style: italic;
            margin-bottom: 2rem;
        }

        /* Formulaire */
        .card-header {
            background: linear-gradient(135deg, #0b3d91, #1f5edb);
            color: #fff;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .card-body {
            background-color: #e6f0ff;
        }
        .form-label {
            font-weight: bold;
            color: #001f3f;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #0b3d91, #3576f6);
            color: white;
            border: none;
            padding: 11px 25px;
            font-weight: bold;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            transform: scale(1.03);
            color: white;
        }

        /* Filtre */
        .ville-filter select {
            max-width: 250px;
        }

        /* Tableau */
        table th {
            background-color: #001f3f; /* bleu marine foncé pour l'entête */
            color: #fff;
            text-align: center;
            font-weight: bold;
        }
        table td {
            text-align: center;
            color: #fff; /* texte blanc */
        }
        /* table tbody tr:nth-child(odd) td {
            background-color: #e6f0ff;
        }
        table tbody tr:nth-child(even) td {
            background-color: #e6f0ff;
        }
        table tbody tr:hover td {
            background-color: #1c70b4;
        } */

        .table-section-title {
            color: #001f3f;
            font-weight: bold;
            margin-top: 3rem;
        }
        .table-section-subtitle {
            color: #3a5f9e;
            font-style: italic;
            margin-bottom: 1rem;
        }

        .alert {
            font-weight: bold;
        }

        /* Conteneur du tableau */
        .table-responsive {
            background-color: #ffffff; /* div blanc */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

    </style>
</head>
<body>
<div class="container mt-4">

    <!-- Titre et description de la page -->
    <h2 class="page-title">Saisie des Achats</h2>
    <p class="page-subtitle text-left">
        Entrez un nouvel achat pour subvenir aux besoins spécifiques des sinistrés.
    </p>

    <!-- Message -->
    <?php if($message): ?>
        <div class="alert alert-<?= $message['type'] ?>"><?= $message['text'] ?></div>
    <?php endif; ?>

    <!-- Formulaire achat -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header">Nouvel Achat</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="ville_id" class="form-label">Ville</label>
                        <select id="ville_id" class="form-select" name="ville_id" required>
                            <option value="">Sélectionnez</option>
                            <?php foreach($villes as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= $v['nom'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="article_id" class="form-label">Article</label>
                        <select id="article_id" class="form-select" name="article_id" required>
                            <option value="">Sélectionnez</option>
                            <?php foreach($articles as $a): ?>
                                <option value="<?= $a['id'] ?>"><?= $a['nom'] ?> (<?= $a['prix_unitaire'] ?> FCFA)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="quantite" class="form-label">Quantité</label>
                        <input type="number" id="quantite" name="quantite" class="form-control" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label for="frais" class="form-label">Frais (%)</label>
                        <input type="number" id="frais" name="frais" class="form-control" value="10" min="0" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-gradient w-100">Valider Achat</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filtre et titre tableau -->
    <h3 class="table-section-title">Liste des Achats</h3>
    <p class="table-section-subtitle">
        Vous pouvez filtrer les achats par ville ou consulter l’ensemble des achats réalisés.
    </p>
    <div class="mb-3 ville-filter">
        <form method="GET">
            <label>Filtrer par ville :</label>
            <select name="ville_filter" class="form-select d-inline-block" onchange="this.form.submit()">
                <option value="">Toutes les villes</option>
                <?php foreach($villes as $v): ?>
                    <option value="<?= $v['id'] ?>" <?= ($_GET['ville_filter'] ?? '') == $v['id'] ? 'selected' : '' ?>><?= $v['nom'] ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Tableau des achats -->
    <div class="table-responsive shadow-sm">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ville</th>
                    <th>Article</th>
                    <th>Quantité</th>
                    <th>Prix Unitaire</th>
                    <th>Frais (%)</th>
                    <th>Montant Total</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($achats) == 0): ?>
                    <tr><td colspan="8">Aucun achat pour le moment</td></tr>
                <?php else: ?>
                    <?php foreach($achats as $a): ?>
                        <tr>
                            <td><?= $a['id'] ?></td>
                            <td><?= $a['ville'] ?></td>
                            <td><?= $a['article'] ?></td>
                            <td><?= $a['quantite'] ?></td>
                            <td><?= number_format($a['prix_unitaire'], 2) ?></td>
                            <td><?= $a['frais_pourcentage'] ?></td>
                            <td><?= number_format($a['montant_total'], 2) ?></td>
                            <td><?= $a['date_achat'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
