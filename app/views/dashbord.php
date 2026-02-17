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

        .btn-reload { background-color: #8e44ad; }
        .btn-reload:hover { background-color: #732d91; }

        .dispatch-mode {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .dispatch-mode label { font-weight: bold; color: #2c3e50; font-size: 14px; }
        .dispatch-mode select {
            padding: 8px 16px;
            border-radius: 6px;
            border: 2px solid #3498db;
            font-size: 14px;
            background: white;
            color: #2c3e50;
            cursor: pointer;
        }
        .mode-tag {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }
        .mode-tag.proportionnel { background: #e67e22; }
        .mode-tag.croissant { background: #27ae60; }

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

        /* Résumé / Totaux */
        .summary-cards {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin: 18px 0;
            flex-wrap: wrap;
        }
        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 12px 18px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            min-width: 140px;
            text-align: center;
        }
        .summary-value {
            font-size: 20px;
            font-weight: 800;
            margin-top: 6px;
            color: #2c3e50;
        }
        .summary-label {
            font-size: 13px;
            color: #7f8c8d;
        }
        .summary-green { border-left: 4px solid #27ae60; }
        .summary-orange { border-left: 4px solid #e67e22; }
        .summary-red { border-left: 4px solid #e74c3c; }
        .summary-blue { border-left: 4px solid #3498db; }

        .dons-card {
            background: white;
            padding: 15px;
            margin-bottom: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #8e44ad;
        }
        .dons-card h2 { color: #8e44ad; }
        .dons-card th { background-color: #8e44ad; }
        .badge-reste {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: bold;
        }

    </style>
</head>
<body>

<h1>Tableau de Bord : Besoins et Dons par Ville</h1>

<?php
$totals = $totals ?? [
    'total_villes' => 0,
    'total_besoins' => 0,
    'total_satisfaits' => 0,
    'total_en_cours' => 0,
    'total_en_attente' => 0,
    'total_quantite_demandee' => 0,
    'total_quantite_attribuee' => 0,
    'total_quantite_restante' => 0,
];
?>

<div class="summary-cards">
    <div class="summary-card summary-blue">
        <div class="summary-label">Villes</div>
        <div class="summary-value"><?= (int)$totals['total_villes'] ?></div>
    </div>

    <div class="summary-card summary-green">
        <div class="summary-label">Satisfaits</div>
        <div class="summary-value"><?= (int)$totals['total_satisfaits'] ?></div>
    </div>

    <div class="summary-card summary-orange">
        <div class="summary-label">En cours</div>
        <div class="summary-value"><?= (int)$totals['total_en_cours'] ?></div>
    </div>

    <div class="summary-card summary-red">
        <div class="summary-label">En attente</div>
        <div class="summary-value"><?= (int)$totals['total_en_attente'] ?></div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Quantité demandée des besoins</div>
        <div class="summary-value"><?= (int)$totals['total_quantite_demandee'] ?></div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Quantité attribuée des besoins</div>
        <div class="summary-value"><?= (int)$totals['total_quantite_attribuee'] ?></div>
    </div>

    <div class="summary-card">
        <div class="summary-label">Quantité restante des besoins </div>
        <div class="summary-value"><?= (int)$totals['total_quantite_restante'] ?></div>
    </div>
</div>

<!-- Choix du mode de dispatch -->
<?php $modeDispatch = $modeDispatch ?? 'date'; ?>
<div class="dispatch-mode">
    <label for="mode-select">📦 Mode de dispatch :</label>
    <select id="mode-select" onchange="document.querySelectorAll('.hidden-mode').forEach(e => e.value = this.value)">
        <option value="date" <?= $modeDispatch === 'date' ? 'selected' : '' ?>>📅 Par date (chronologique)</option>
        <option value="proportionnel" <?= $modeDispatch === 'proportionnel' ? 'selected' : '' ?>>⚖️ Par proportion</option>
        <option value="croissant" <?= $modeDispatch === 'croissant' ? 'selected' : '' ?>>📈 Par ordre croissant des besoins</option>
    </select>
</div>
<!-- Boutons -->
<div class="top-bar">
    <form method="POST" action="recharger" style="display: inline;">
        <button type="submit" class="btn btn-reload" onclick="return confirm('⚠️ Attention ! Cela va SUPPRIMER toutes les répartitions et remettre tous les dons comme non répartis. Continuer ?')">🔄 Recharger</button>
    </form>
    <form method="POST" action="simulate" style="display: inline;">
        <input type="hidden" name="mode" value="<?= htmlspecialchars($modeDispatch) ?>" class="hidden-mode">
        <button type="submit" class="btn btn-simulate">📋 Simuler</button>
    </form>
    <form method="POST" action="dispatch" style="display: inline;">
        <input type="hidden" name="mode" value="<?= htmlspecialchars($modeDispatch) ?>" class="hidden-mode">
        <button type="submit" class="btn btn-validate" onclick="return confirm('Confirmer le dispatch réel de TOUS les dons ?')">✅ Valider</button>
    </form>
    <form method="GET" action="/" style="display: inline;">
        <button type="submit" class="btn btn-cancel">❌ Annuler</button>
    </form>
</div>

<!-- Dons non encore répartis -->
<?php $donsNonRepartis = $donsNonRepartis ?? []; ?>
<?php if (!empty($donsNonRepartis)): ?>
<div class="dons-card">
    <h2>🎁 Dons non encore répartis (<?= count($donsNonRepartis) ?>)</h2>
    <table>
        <tr>
            <th>Don #</th>
            <th>Article</th>
            <th>Donateur</th>
            <th>Quantité totale</th>
            <th>Déjà répartie</th>
            <th>Reste à répartir</th>
        </tr>
        <?php foreach ($donsNonRepartis as $don): ?>
        <tr>
            <td><?= $don['id'] ?></td>
            <td><?= htmlspecialchars($don['article']) ?></td>
            <td><?= htmlspecialchars($don['donateur']) ?></td>
            <td><?= $don['quantite_totale'] ?></td>
            <td><?= $don['quantite_repartie'] ?></td>
            <td><span class="badge-reste"><?= $don['reste'] ?></span></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php else: ?>
<div class="dons-card" style="text-align:center; border-left-color: #27ae60;">
    <h2 style="color:#27ae60;">✅ Tous les dons ont été entièrement répartis</h2>
</div>
<?php endif; ?>

<?php if (isset($showSimulation) && $showSimulation): ?>
    <div class="info-simulation">
        ⚠️ Mode simulation 
        <span class="mode-tag <?= ($modeDispatch ?? 'date') === 'proportionnel' ? 'proportionnel' : (($modeDispatch ?? 'date') === 'croissant' ? 'croissant' : '') ?>">
            <?php 
                $mode = $modeDispatch ?? 'date';
                if ($mode === 'proportionnel') echo '⚖️ Proportionnel';
                elseif ($mode === 'croissant') echo '📈 Ordre croissant';
                else echo '📅 Par date';
            ?>
        </span>
        — Les lignes en <span style="background: #fff9c4; padding: 2px 6px;">jaune</span> montrent les nouvelles distributions 
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