<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Besoin</title>
</head>
<body>
    <h2>Enregistrer un besoin pour une ville</h2>
    <form action="enregistrer_besoin.php" method="POST">
        <!-- Ville -->
        <label for="ville">Ville :</label>
        <select id="ville" name="ville" required>
            <option value="">--Sélectionnez une ville--</option>
            <option value="Antananarivo">Antananarivo</option>
            <option value="Toamasina">Toamasina</option>
            <option value="Fianarantsoa">Fianarantsoa</option>
            <!-- Ajoute toutes les villes ici -->
        </select>
        <br><br>

        <!-- Produit -->
        <label for="produit">Produit :</label>
        <select id="produit" name="produit" required>
            <option value="">--Sélectionnez un produit--</option>
            <option value="Riz">Riz</option>
            <option value="Huile">Huile</option>
            <option value="Tôle">Tôle</option>
            <option value="Clou">Clou</option>
            <option value="Argent">Argent</option>
            <!-- Ajoute tous les produits ici -->
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
