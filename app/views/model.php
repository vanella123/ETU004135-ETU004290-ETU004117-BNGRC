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
    <style>
        
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
