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
        /* Corps de page doux et clair */
        body {
            background-color: #f8f9fa; /* gris clair agréable */
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Navbar et footer bleu subtil */
        .navbar, .footer {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            color: white;
            border-radius: 0 0 10px 10px;
        }

        /* Liens de navigation */
        .navbar a, .footer a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .navbar a:hover, .footer a:hover {
            color: #ffd966; /* doré clair au hover */
        }

        /* Conteneur principal */
        .container {
            margin-top: 25px;
        }

        /* Boutons primaires modernes */
        .btn-primary {
            background-color: #3a5f9e;
            border-color: #2e4c7b;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #2e4c7b;
        }

        /* Cartes et sections */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 20px;
        }

        /* Footer centré et léger */
        .footer {
            padding: 15px 0;
            text-align: center;
            font-size: 0.9rem;
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
