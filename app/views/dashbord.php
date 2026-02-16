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
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            color: white;
            border: none;
            padding: 12px 25px;
            font-size: 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-simulate { background-color: #3498db; }
        .btn-simulate:hover { background-color: #2980b9; }

        .btn-validate { background-color: #27ae60; }
        .btn-validate:hover { background-color: #229954; }

        .btn-cancel { background-color: #95a5a6; }
        .btn-cancel:hover { background-color: #7f8c8d; }

        .ville-card {
            background: white;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        h2 { margin-bottom: 10px; color: #2c3e50; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th { background-color: #34495e; color: white; }

        .status-satisfait { color: #27ae60; font-weight: bold; }
        .status-en-cours  { color: #e67e22; font-weight: bold; }
        .status-en-attente { color: #e74c3c; font-weight: bold; }

        /* Ligne simulée : fond jaune */
        .row-simulated { background-color: #fff9c4 !important; }

        .sim-badge {
            display: inline-block;
            background: #f39c12;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .info-simulation {
            background: #fff3cd;
            border: 2px solid #f39c12;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 20px;
            text-align: center;
            color: #856404;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Tableau de Bord : Besoins et Dons par Ville</h1>

<!-- 3 boutons toujours visibles -->
<div class="top-bar">
    <form method="POST" action="simulate" style="display: inline;">
        <button type="submit" class="btn btn-simulate">📋 Simuler</button>
    </form>
    <form method="POST" action="dispatch" style="display: inline;">
        <button type="submit" class="btn btn-validate" onclick="return confirm('Confirmer le dispatch réel de TOUS les dons ?')">✅ Valider</button>
    </form>
    <form method="GET" action="/" style="display: inline;">
        <button type="submit" class="btn btn-cancel">❌ Annuler</button>
    </form>
</div>

<?php if (isset($showSimulation)): ?>
    <div class="info-simulation">
        ⚠️ Mode simulation — Les lignes en <span style="background: #fff9c4; padding: 2px 6px;">jaune</span> montrent les nouvelles distributions (pas encore en base)
    </div>
<?php endif; ?>

<?php
// Index des ajouts simulés par besoin_id
$simulationIndex = [];
if (isset($showSimulation) && isset($simulation) && !empty($simulation['besoins'])) {
    foreach ($simulation['besoins'] as $b) {
        $simulationIndex[$b['id']] = $b;
    }
}

$currentVille = null;

foreach ($dashboard as $row):
    $simData = $simulationIndex[$row['besoin_id']] ?? null;
    $hasSimulation = $simData && $simData['ajout_simule'] > 0;

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
                <th>Quantité Demandée</th>
                <th>Quantité Attribuée</th>
                <th>Quantité Restante</th>
                <th>Statut</th>
            </tr>
<?php endif; ?>

            <tr class="<?= $hasSimulation ? 'row-simulated' : '' ?>">
                <td><?= htmlspecialchars($row['article']) ?></td>
                <td><?= $row['quantite_demandee'] ?></td>

                <?php if ($hasSimulation): ?>
                    <td>
                        <?= $row['quantite_attribuee'] ?>
                        <span class="sim-badge">+<?= $simData['ajout_simule'] ?></span>
                    </td>
                    <td><?= $simData['nouveau_reste'] ?></td>
                    <td>
                        <?php
                        if ($simData['nouveau_reste'] == 0 && $simData['nouveau_attribue'] > 0):
                            echo '<span class="status-satisfait">Satisfait</span>';
                        elseif ($simData['nouveau_attribue'] > 0):
                            echo '<span class="status-en-cours">En cours</span>';
                        else:
                            echo '<span class="status-en-attente">En attente</span>';
                        endif;
                        ?>
                    </td>
                <?php else: ?>
                    <td><?= $row['quantite_attribuee'] ?></td>
                    <td><?= $row['quantite_restante'] ?></td>
                    <td>
                        <?php
                        $statusClass = match($row['statut']) {
                            'Satisfait' => 'status-satisfait',
                            'En cours'  => 'status-en-cours',
                            default     => 'status-en-attente',
                        };
                        ?>
                        <span class="<?= $statusClass ?>"><?= htmlspecialchars($row['statut']) ?></span>
                    </td>
                <?php endif; ?>
            </tr>

<?php endforeach; ?>

<?php if ($currentVille !== null): ?>
</table>
</div>
<?php endif; ?>

</body>
</html>