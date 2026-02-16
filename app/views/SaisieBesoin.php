<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Formulaire de Besoin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 40px;
        }

        .alert-success,
        .alert-error {
            max-width: 500px;
            margin: 20px auto;
            padding: 12px 16px;
            border-radius: 6px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #e6f4ea;
            color: #1e4620;
        }

        .alert-error {
            background-color: #fdecea;
            color: #611a15;
        }

        form {
            background-color: #fff;
            max-width: 500px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        select, input[type="number"], input[type="date"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        option {
            padding: 5px;
        }
    </style>
</head>

<body>
    <h2>Enregistrer un besoin pour une ville</h2>
    <?php if (!empty($feedback)) { ?>
        <div class="<?= !empty($feedback['success']) ? 'alert-success' : 'alert-error' ?>">
            <?= htmlspecialchars($feedback['message'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php } ?>
    <form action="/saisie" method="POST">
        <!-- Ville -->
        <label for="ville">Ville :</label>
        <select id="ville" name="ville_id" required>
            <option value="">--Sélectionnez une ville--</option>
            <?php foreach($villes as $ville) { ?>
            <option value="<?= $ville['id'] ?>"><?= $ville['nom'] ?></option>
            <?php } ?>
        </select>
        <br><br>
        
        <!-- Produit -->
        <label for="produit">Produit :</label>
        <select id="produit" name="article_id" required>
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

        <!-- Date de saisie -->
        <label for="date_saisie">Date de saisie :</label>
        <input type="date" id="date_saisie" name="date_saisie" required>
        <br><br>

        <input type="submit" value="Enregistrer le besoin">
    </form>
    
</body>
</html>
