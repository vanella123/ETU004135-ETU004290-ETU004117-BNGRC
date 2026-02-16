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
            margin-bottom: 10px;
        }

        .top-bar {
            text-align: center;
            margin-bottom: 25px;
        }

        .btn-dispatch {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-dispatch:hover {
            background-color: #27ae60;
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

        .status-satisfait {
            color: #27ae60;
            font-weight: bold;
        }

        .status-en-cours {
            color: #e67e22;
            font-weight: bold;
        }

        .status-attente {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Tableau de Bord : Besoins et Dons par Ville</h1>

<div class="top-bar">
    <form method="POST" action="dispatch">
        <button type="submit" class="btn-dispatch" onclick="return confirm('Lancer le dispatch de tous les dons non distribues ?')">
            Dispatcher les dons
        </button>
    </form>
</div>

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
        <h2>Ville : <?= htmlspecialchars($row['ville']) ?></h2>
        <table>
            <tr>
                <th>Article</th>
                <th>Quantite Demandee</th>
                <th>Quantite Attribuee</th>
                <th>Quantite Restante</th>
                <th>Statut</th>
            </tr>

<?php endif; ?>

            <tr>
                <td><?= htmlspecialchars($row['article']) ?></td>
                <td><?= $row['quantite_demandee'] ?></td>
                <td><?= $row['quantite_attribuee'] ?></td>
                <td><?= $row['quantite_restante'] ?></td>
                <td>
                    <?php if ($row['quantite_restante'] == 0 && $row['quantite_attribuee'] > 0): ?>
                        <span class="status-satisfait">Satisfait</span>
                    <?php elseif ($row['quantite_attribuee'] > 0 && $row['quantite_restante'] > 0): ?>
                        <span class="status-en-cours">En cours</span>
                    <?php else: ?>
                        <span class="status-attente">Pas de dons</span>
                    <?php endif; ?>
                </td>
            </tr>

<?php endforeach; ?>

<?php if ($currentVille !== null): ?>
</table>
</div>
<?php endif; ?>

</body>
</html>