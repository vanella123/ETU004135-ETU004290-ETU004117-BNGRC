<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Besoin</title>
</head>
<body>
    <h2>Enregistrer un besoin pour une ville</h2>
    <form action="/saisieBesoin" method="POST">
        <!-- Ville -->
        <label for="ville">Ville :</label>
        <select id="ville" name="ville" required>
            <option value="">--Sélectionnez une ville--</option>
            <?php foreach($villes as $ville) { ?>
            <option value="<?= $ville['id'] ?>"><?= $ville['nom'] ?></option>
            <?php } ?>
        </select>
        <br><br>
        
        <!-- Produit -->
        <label for="produit">Produit :</label>
        <select id="produit" name="produit" required>
            <option value="">--Sélectionnez un produit--</option>
            <?php foreach($produits as $produit) { ?>
            <option value="<?= $produit['id'] ?>"><?= $produit['nom'] ?></option>
            <?php } ?>
        </select>
        <br><br>

        <!-- Quantité -->
        <label for="quantite">Quantité :</label>
        <input type="number" id="quantite" name="quantite" min="1" required>
        <br><br>

        <!-- Date de livraison -->
        <label for="date_livraison">Date de livraison :</label>
        <input type="date" id="date_livraison" name="date_livraison" required>
        <br><br>

        <input type="submit" value="Enregistrer le besoin">
    </form>
</body>
</html>
