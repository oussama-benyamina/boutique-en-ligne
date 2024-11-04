<?php
// Inclure la connexion à la base de données
require 'functions/db_conn.php';

if (isset($_GET['query'])) {
    // Récupérer le terme de recherche entré par l'utilisateur
    $searchTerm = $_GET['query'];

    // Préparer la requête SQL avec LIKE pour une recherche partielle
    $query = $pdo->prepare("
        SELECT * 
        FROM products 
        WHERE name LIKE :searchTerm 
           OR description LIKE :searchTerm 
           OR category LIKE :searchTerm 
           OR brand LIKE :searchTerm
        ORDER BY name ASC
    ");

    // Exécuter la requête avec le terme de recherche
    $query->execute(['searchTerm' => '%' . $searchTerm . '%']);

    // Récupérer les résultats
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    $searchTerm = ''; // Définir un terme de recherche vide s'il n'y a pas de query param
    $results = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link rel="stylesheet" href="assets/css/shop.css"> <!-- Inclure le fichier CSS global de votre site -->
    <?php include 'includes/_head-index.php' ?>
    <style>
        /* Custom CSS spécifique à la page de résultats de recherche */
        .search-results-container {
            padding: 20px 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .search-results-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 40px;
            color: #2F9985;
            position: relative;
        }

        .search-results-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: #2F9985;
        }

        .search-results-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .search-results-item {
            background: white;
            border: 1px solid #eee;
            padding: 15px;
            text-align: center;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .search-results-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(47, 153, 133, 0.2);
        }

        .image-container {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 Aspect Ratio */
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .search-results-item img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain; /* Changed from cover to contain */
            object-position: center;
            transition: transform 0.3s ease;
            background-color: #f8f8f8; /* Light background for images */
        }

        .search-results-item:hover img {
            transform: scale(1.05);
        }

        .search-results-item h2 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #2F9985;
            font-weight: 600;
        }

        .search-results-item p {
            margin: 10px 0;
            color: #666;
            line-height: 1.6;
        }

        .product-price {
            font-size: 1.25rem;
            color: #2F9985;
            font-weight: bold;
            margin: 15px 0;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-top: 1px solid #eee;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        .product-meta span {
            color: #888;
        }

        .no-results {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #666;
            background: #f9f9f9;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 600px;
        }

        @media (max-width: 1200px) {
            .search-results-list {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .search-results-list {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .image-container {
                padding-top: 100%; /* 1:1 Aspect Ratio for mobile */
            }
        }

        @media (max-width: 480px) {
            .search-results-list {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 10px;
            }

            .search-results-item {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/_header.php' ?>
    <div class="search-results-container">
        <h1 class="search-results-title">Résultats de recherche pour "<?php echo htmlspecialchars($searchTerm); ?>"</h1>

        <?php if (!empty($results)): ?>
            <div class="search-results-list">
                <?php foreach ($results as $product): ?>
                    <div class="search-results-item">
                        <a href="single-product.php?id=<?= $product['product_id']; ?>" style="text-decoration: none; color: inherit;">
                            <div class="image-container">
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     loading="lazy">
                            </div>
                            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                            <p><?php echo htmlspecialchars(substr($product['description'], 0, 100)) . '...'; ?></p>
                            <div class="product-price"><?php echo htmlspecialchars($product['price']); ?> €</div>
                            <div class="product-meta">
                                <span><strong>Marque:</strong> <?php echo htmlspecialchars($product['brand']); ?></span>
                                <span><strong>Catégorie:</strong> <?php echo htmlspecialchars($product['category']); ?></span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <p>Aucun produit ne correspond à votre recherche.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php include 'includes/_footer.php' ?>
    <?php include 'includes/_register-login.php' ?>
</body>

</html>
</html>