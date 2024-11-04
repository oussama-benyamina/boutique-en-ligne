<?php
// Inclure la connexion à la base de données
require 'functions/db_conn.php'; 

// Chemin du dossier contenant les images
$imageDirectory = 'assets/img/products/';

// Récupérer tous les fichiers du dossier
$images = glob($imageDirectory . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// Initialiser un compteur pour les nouveaux noms de fichiers
$imageCounter = 1;

foreach ($images as $image) {
    // Nouveau nom de fichier
    $newFileName = $imageDirectory . 'watch-img-' . $imageCounter . '.jpg';

    // Renommer le fichier
    if (rename($image, $newFileName)) {
        // Mettre à jour le chemin de l'image dans la base de données
        $sql = "UPDATE products SET image_url = :newFileName WHERE image_url = :oldFileName";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':newFileName' => $newFileName,
            ':oldFileName' => $image,
        ]);
        echo "Image renamed to $newFileName and database updated.<br>";
    } else {
        echo "Failed to rename $image.<br>";
    }

    $imageCounter++;
}

echo "Renaming process completed!";
?>
