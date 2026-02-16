<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <table>
        <tr>
            <th>ID</th>
            <th>Ville</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Date de saisie</th>
        </tr>
        <?php foreach($besoins as $besoin) { ?>
        <tr>
            <td><?= $besoin['id'] ?></td>
            <td><?= $besoin['ville'] ?></td>
            <td><?= $besoin['article'] ?></td>
            <td><?= $besoin['quantite'] ?></td>
            <td><?= $besoin['date_saisie'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>