<!DOCTYPE html>
<html>
<head>
    <title>Simulation du Dispatch des Dons</title>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-simulate {
            background-color: #3498db;
            color: white;
        }

        .btn-simulate:hover {
            background-color: #2980b9;
        }

        .btn-validate {
            background-color: #27ae60;
            color: white;
        }

        .btn-validate:hover {
            background-color: #229954;
        }

        .btn-cancel {
            background-color: #95a5a6;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #7f8c8d;
        }

        .simulation-result {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f4fd;
            border-left: 4px solid #3498db;
            border-radius: 5px;
        }

        .don-group {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .don-header {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .repartition-list {
            margin-left: 20px;
        }

        .repartition-item {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }

        .no-dispatch {
            text-align: center;
            color: #7f8c8d;
            font-style: italic;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #34495e;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Simulation du Dispatch des Dons</h1>

        <div class="button-group">
            <form method="POST" action="simulation/preview" style="display: inline;">
                <button type="submit" class="btn btn-simulate">
                    📋 Simuler le Dispatch
                </button>
            </form>

            <form method="POST" action="dispatch" style="display: inline;">
                <button type="submit" class="btn btn-validate" onclick="return confirm('Confirmer le dispatch de TOUS les dons non distribués ?')">
                    ✅ Valider et Exécuter
                </button>
            </form>

            <a href="/" class="btn btn-cancel">
                ❌ Annuler
            </a>
        </div>

        <?php if (isset($simulation) && !empty($simulation)): ?>
            <div class="simulation-result">
                <h2>📊 Résultat de la Simulation</h2>
                
                <?php foreach ($simulation as $donData): ?>
                    <div class="don-group">
                        <div class="don-header">
                            🎁 Don #<?= $donData['don_id'] ?> - Article #<?= $donData['article_id'] ?> 
                            (Reste: <?= $donData['reste'] ?> unités)
                        </div>

                        <?php if (!empty($donData['repartitions'])): ?>
                            <table>
                                <thead>
                                    <tr>
                                        <th>🏙️ Ville</th>
                                        <th>🆔 Besoin ID</th>
                                        <th>📦 Quantité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($donData['repartitions'] as $rep): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rep['ville']) ?></td>
                                            <td>#<?= $rep['besoin_id'] ?></td>
                                            <td><?= $rep['quantite'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div style="color: #e74c3c; font-style: italic;">
                                Aucune répartition possible pour ce don.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php elseif (isset($simulation)): ?>
            <div class="no-dispatch">
                <h3>✅ Tous les dons ont déjà été distribués</h3>
                <p>Il n'y a actuellement aucun don en attente de dispatch.</p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 30px; text-align: center; color: #7f8c8d;">
            <p><strong>💡 Instructions :</strong></p>
            <p>• <strong>Simuler</strong> : Voir un aperçu sans modifier la base de données</p>
            <p>• <strong>Valider</strong> : Exécuter réellement le dispatch des dons</p>
            <p>• <strong>Annuler</strong> : Retourner au tableau de bord</p>
        </div>
    </div>
</body>
</html>