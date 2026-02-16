<!DOCTYPE html>
<html>
<head>
    <title>Tableau de Bord - Besoins et Dons</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .ville-card {
            background: white;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        .ok {
            color: green;
            font-weight: bold;
        }

        .reste {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>📊 Tableau de Bord : Besoins et Dons par Ville</h1>

<?php
$currentVille = null;

foreach ($dashboard as $row):

    // Commencer une nouvelle ville
    if ($currentVille != $row['ville']):
        if ($currentVille != null):
            echo "</table></div>";
        endif;

        $currentVille = $row['ville'];
?>

    <div class="ville-card">
        <h2>🏙 Ville : <?= htmlspecialchars($row['ville']) ?></h2>
        <table>
            <tr>
                <th>Article</th>
                <th>Quantité Demandée</th>
                <th>Quantité Attribuée</th>
                <th>Quantité Restante</th>
                <th>Etat</th>
            </tr>

<?php endif; ?>

            <tr>
                <td><?= htmlspecialchars($row['article']) ?></td>
                <td><?= $row['quantite_demandee'] ?></td>
                <td><?= $row['quantite_attribuee'] ?></td>
                <td><?= $row['quantite_restante'] ?></td>
                <td>
                    <?php if ($row['quantite_restante'] == 0): ?>
                        <span class="ok">Satisfait</span>
                    <?php else: ?>
                        <span class="reste">En attente</span>
                    <?php endif; ?>
                </td>
            </tr>

<?php endforeach; ?>

</table>
</div>

</body>
</html>