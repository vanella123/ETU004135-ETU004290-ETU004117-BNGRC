<?php
// Variables attendues : $articles, $dons, $message
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Nouveau Don</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        /* background-color: #f8f9fa; gris clair */
        /* font-family: 'Times New Roman'; */
    }

    h2, h3 {
        color: #0b3d91; /* bleu marine */
        margin-bottom: 20px;
    }

    table { margin-top: 20px; text-align: center; }
    /* Tableau dons */
    .table-dons {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table-dons thead {
        background-color: #0b3d91; /* bleu marine */
        color: white;
    }

    .table-dons thead th {
        padding: 12px;
        text-align: center;
        font-weight: bold;
    }

    .table-dons tbody {
        background-color: #ffffff; /* blanc */
    }

    .table-dons tbody td {
        padding: 10px;
        text-align: center;
        color: #0b3d91; /* texte bleu */
        border-bottom: 1px solid #0b3d91; /* ligne de séparation bleu */
    }

    .table-dons tbody tr:hover {
        background-color: #f2f6ff; /* léger bleu au hover */
    }

    .btn-gradient {
        background: linear-gradient(135deg, #0b3d91, #1f5edb);
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

</style>

</head>
<body>
<div class="container mt-5">
    <h2>Ajouter un nouveau don</h2>

    <?php if(!empty($message)): ?>
        <div class="alert alert-<?= $message['type'] ?>"><?= $message['text'] ?></div>
    <?php endif; ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="donateur" class="form-label">Nom du donateur</label>
            <input type="text" class="form-control" id="donateur" name="donateur" required>
        </div>

        <div class="mb-3">
            <label for="article_id" class="form-label">Article</label>
            <select class="form-select" id="article_id" name="article_id" required>
                <option value="">-- Sélectionner un article --</option>
                <?php foreach ($articles as $article): ?>
                    <option value="<?= $article['id'] ?>"><?= $article['nom'] ?> (<?= $article['prix_unitaire'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" class="form-control" id="quantite" name="quantite" min="1" required>
        </div>

        <div class="mb-3">
            <label for="date_saisie" class="form-label">Date</label>
            <input type="date" class="form-control" id="date_saisie" name="date_saisie" required>
        </div>

        <button type="submit" class="btn btn-gradient">Valider</button>
    </form>

    <h3 class="mt-5">Liste des dons</h3>
    <table class="table-dons">
        <thead>
            <tr>
                <th>Donateur</th>
                <th>Article</th>
                <th>Quantité</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dons as $don): ?>
                <tr>
                    <td><?= htmlspecialchars($don['donateur_nom']) ?></td>
                    <td><?= htmlspecialchars($don['date_saisie']) ?></td>
                    <td><?= htmlspecialchars($don['article']) ?></td>
                    <td><?= htmlspecialchars($don['quantite']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</div>
</body>
</html>
