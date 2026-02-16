<?php
// layout.php : modèle principal
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? "Mon Application Dons" ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS général -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        /* Thème bleu avec nuances */
        body {
            background-color: #f0f4f8;
            color: #333;
        }

        .navbar, .footer {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: white;
        }

        .navbar a, .footer a {
            color: white;
            text-decoration: none;
        }

        .container {
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #2a5298;
            border-color: #1e3c72;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <?php
            // Inclusion de la page spécifique
            if (isset($content)) {
                include $content;
            }
        ?>
    </div>

    <?php include 'footer.php'; ?>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
