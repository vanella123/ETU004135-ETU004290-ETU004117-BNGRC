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
    table { margin-top: 20px; text-align: center; }
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

        <button type="submit" class="btn btn-primary">Valider</button>
    </form>

    <h3 class="mt-5">Liste des dons</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Donateur</th>
                <th>Article</th>
                <th>Quantité</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dons as $don): ?>
                <tr>
                    <td><?= $don['id'] ?></td>
                    <td><?= $don['donateur'] ?? 'N/A' ?></td>
                    <td><?= $don['article'] ?? '' ?></td>
                    <td><?= $don['quantite'] ?></td>
                    <td><?= $don['date_saisie'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
