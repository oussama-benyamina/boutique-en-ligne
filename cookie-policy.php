<?php
session_start();
require 'functions/db_conn.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include 'includes/_head-index.php'; ?>
    <title>Politique de Cookies - Votre Site Web</title>
    <style>
        .cookie-policy-content {
            background-color: #fff;
            color: #051922;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .cookie-policy-content h2 {
            color: #2F9985;
            margin-bottom: 20px;
        }
        .cookie-policy-content h3 {
            color: #2F9985;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        .cookie-policy-content p, .cookie-policy-content ul {
            margin-bottom: 15px;
            line-height: 1.6;
        }
        .cookie-policy-content ul {
            padding-left: 20px;
        }
        .cookie-policy-content li {
            margin-bottom: 10px;
        }
        .cookie-policy-content strong {
            color: #051922;
        }
    </style>
</head>
<body>
    <?php include 'includes/_header.php'; ?>

    <div class="breadcrumb-section breadcrumb-bg">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 offset-lg-2 text-center">
                    <div class="breadcrumb-text">
                        <p>Informations sur l'utilisation des cookies</p>
                        <h1>Politique de Cookies</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-150 mb-150">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cookie-policy-content">
                        <h2>Politique de Cookies</h2>
                        <p>Dernière mise à jour : <?php echo date("d/m/Y"); ?></p>

                        <h3>1. Qu'est-ce qu'un cookie ?</h3>
                        <p>Un cookie est un petit fichier texte déposé sur votre terminal (ordinateur, tablette ou mobile) lors de la visite d'un site web. Il permet au site de mémoriser des informations sur votre visite, comme votre langue préférée et d'autres paramètres.</p>

                        <h3>2. Comment utilisons-nous les cookies ?</h3>
                        <p>Nous utilisons différents types de cookies pour les finalités suivantes :</p>
                        <ul>
                            <li>Cookies strictement nécessaires : Ces cookies sont essentiels au fonctionnement de notre site web.</li>
                            <li>Cookies de performance : Ces cookies nous aident à comprendre comment les visiteurs interagissent avec notre site web.</li>
                            <li>Cookies de fonctionnalité : Ces cookies permettent à notre site web de se souvenir des choix que vous faites et de vous offrir des fonctionnalités améliorées et personnalisées.</li>
                            <li>Cookies de ciblage ou publicitaires : Ces cookies sont utilisés pour diffuser des publicités plus pertinentes pour vous et vos intérêts.</li>
                        </ul>

                        <h3>3. Comment gérer vos préférences en matière de cookies ?</h3>
                        <p>Lors de votre première visite sur notre site, un bandeau vous informe de l'utilisation des cookies. Vous pouvez à tout moment modifier vos préférences en matière de cookies en cliquant sur le lien "Gérer mes cookies" en bas de chaque page de notre site.</p>

                        <h3>4. Liste des cookies utilisés</h3>
                        <p>Voici la liste des cookies que nous utilisons :</p>
                        <ul>
                            <li><strong>cookie_consent</strong> : Ce cookie enregistre vos préférences concernant l'utilisation des cookies sur notre site. Il est essentiel pour respecter votre choix d'accepter ou de refuser les cookies.</li>
                            
                            <li><strong>session_id</strong> : Ce cookie est utilisé pour identifier votre session unique sur notre site. Il est essentiel pour le fonctionnement de notre plateforme e-commerce, notamment pour gérer votre panier d'achat.</li>
                            
                            <li><strong>user_id</strong> : Si vous vous connectez à votre compte, ce cookie est utilisé pour vous maintenir connecté et personnaliser votre expérience sur notre site.</li>
                            
                            <li><strong>language</strong> : Ce cookie mémorise la langue que vous avez choisie pour naviguer sur notre site.</li>
                            
                            <li><strong>_ga, _gid, _gat</strong> : Ces cookies sont utilisés par Google Analytics pour collecter des informations sur la façon dont les visiteurs utilisent notre site. Nous utilisons ces informations pour compiler des rapports et nous aider à améliorer notre site.</li>
                            
                            <li><strong>_fbp</strong> : Ce cookie est défini par Facebook pour diffuser des publicités lorsque vous visitez notre site web et que vous êtes sur Facebook ou une plateforme numérique alimentée par la publicité Facebook.</li>
                            
                            <li><strong>recently_viewed</strong> : Ce cookie enregistre les produits que vous avez récemment consultés sur notre site, afin de vous les suggérer lors de vos prochaines visites.</li>
                            
                            <li><strong>cart_items</strong> : Ce cookie conserve les informations sur les articles dans votre panier d'achat, même si vous quittez notre site et y revenez plus tard.</li>
                            
                            <li><strong>currency</strong> : Ce cookie mémorise la devise que vous préférez utiliser pour afficher les prix sur notre site.</li>
                            
                            <li><strong>newsletter_popup</strong> : Ce cookie nous aide à déterminer si nous devons vous montrer la fenêtre pop-up d'inscription à la newsletter.</li>
                        </ul>

                        <p>Veuillez noter que certains de ces cookies sont essentiels au fonctionnement de notre site, tandis que d'autres nous aident à améliorer votre expérience en personnalisant le contenu et les publicités. Vous pouvez gérer vos préférences en matière de cookies à tout moment via le lien "Gérer mes cookies" en bas de chaque page.</p>

                        <h3>5. Durée de conservation des cookies</h3>
                        <p>Les cookies que nous utilisons ont une durée de vie maximale de 13 mois, conformément aux recommandations de la CNIL.</p>

                        <h3>6. Vos droits</h3>
                        <p>Conformément au Règlement Général sur la Protection des Données (RGPD) et à la Loi Informatique et Libertés, vous disposez d'un droit d'accès, de rectification, d'effacement, et de portabilité des données vous concernant, ainsi que d'un droit d'opposition et de limitation du traitement. Pour exercer ces droits, vous pouvez nous contacter à l'adresse suivante : [votre adresse email de contact].</p>

                        <h3>7. Modifications de notre politique de cookies</h3>
                        <p>Nous nous réservons le droit de modifier cette politique de cookies à tout moment. Toute modification prendra effet immédiatement. Nous vous encourageons à consulter régulièrement cette page pour prendre connaissance des éventuelles modifications.</p>

                        <h3>8. Nous contacter</h3>
                        <p><strong>Si vous avez des questions concernant notre politique de cookies, veuillez nous contacter à : <span style="font-size: 1.2em; color: #2F9985;">ynextwach@gmail.com</span></strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/_footer.php'; ?>
    <?php include 'includes/_register-login.php'; ?>
</body>
</html>
