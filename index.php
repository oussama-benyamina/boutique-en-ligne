<?php
require 'functions/db_conn.php';
session_start();

// Clear any remaining session data if user just logged out
if (isset($_SESSION['logout_flag'])) {
    session_unset();
    session_destroy();
    session_start();
}

if (!isset($_SESSION['client_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'admins') {

}

// Include your database connection file
include 'functions/db_conn.php';

// Récupérer les 3 derniers produits ajoutés
$sql = "SELECT * FROM products ORDER BY product_id DESC LIMIT 3";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$latest_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="manifest" href="./manifest.json">
	<?php include 'includes/_head-index.php'; ?>
	<style>
        .cart-btn {
            background: #2F9985;
            border: 1px solid #2F9985;
            padding: 8px;
            color: white;
            border-radius: 25px;
        }

        .cart-btn:hover {
            background: black;
            transition: 1s;
        }

        .filter-form {
            margin: 30px 0;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .filter-form .form-group {
            margin-bottom: 15px;
        }
        .filter-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .filter-form input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 16px;
        }
        .filter-btn {
            background-color: #2F9985;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .filter-btn:hover {
            background-color: #247a6b;
        }

        .testimonial-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .single-testimonial-slider {
            flex: 0 0 auto;
            transition: all 0.3s ease;
        }

        .side-slide {
            width: 25%;
            transform: scale(0.9);
        }

        .center-slide {
            width: 35%;
            transform: scale(1.1);
        }

        .client-avater img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        /* Adjust the slick slider settings if needed */
        .testimonial-sliders .slick-slide {
            margin: 0 10px;
        }

        .testimonial-sliders .slick-list {
            padding: 30px 0;
        }

        .testimonial-group {
            padding: 30px 0;
        }

        .owl-carousel .owl-item {
            transition: all 0.3s ease;
            opacity: 0.8;
            transform: scale(0.95);
        }

        .owl-carousel .owl-item.center {
            opacity: 1;
            transform: scale(1.05);
            z-index: 2;
        }

        .owl-carousel .owl-item:not(.center) {
            transform: scale(0.9);
            opacity: 0.8;
        }

        .owl-carousel .owl-stage-outer {
            padding: 30px 0;
        }

        .client-avater img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            object-fit: cover;
        }

        .team-member {
            text-align: center;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .member-img {
            width: 100%;
            padding-bottom: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .member-img img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .owl-carousel .owl-stage-outer {
            padding: 30px 0;
        }

        .owl-carousel .owl-item {
            transition: all 0.3s ease;
            opacity: 0.8;
            transform: scale(0.95);
        }

        .owl-carousel .owl-item.active.center {
            opacity: 1;
            transform: scale(1.05);
            z-index: 2;
        }

        /* Mobile styles */
        @media (max-width: 768px) {
            .team-member {
                padding: 15px;
                max-width: 80vw;
                margin: 0 auto;
            }
            
            .member-img {
                width: 100%;
                padding-bottom: 100%;
            }
            
            .owl-carousel .owl-item {
                transition: all 0.3s ease;
                opacity: 0.5;
                transform: scale(0.8);
            }
            
            .owl-carousel .owl-item.active {
                opacity: 1;
                transform: scale(1.1);
                z-index: 2;
            }
        }

        /* Smaller mobile devices */
        @media (max-width: 576px) {
            .team-member {
                max-width: 90vw;
            }
            
            .team-member h3 {
                font-size: 16px;
                margin-top: 10px;
            }
            
            .team-member span {
                font-size: 12px;
            }
        }

        

        .about-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
    </style>

    <link rel="stylesheet" href="assets/css/owl.carousel.css">
    <script src="assets/js/jquery-1.11.3.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>

</head>
<body>
	
	
	
	<?php include 'includes/_header.php'; ?>

	<!-- hero area -->
	<div class="hero-area hero-bg">
		<div class="container" style="position: relative;">
			<div style="position: absolute; top:0; left:50%; transform: translateX(-50%); width:100vw; height:100vh; overflow:hidden">
				<video autoplay muted loop class="video-bg" style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 100vw; height: 100vh; object-fit: cover;" controlsList="nodownload noplaybackrate; backdrop-filter: drop-shadow(4px 4px 10px black);">
					<source src="video/videobg.mp4" type="video/mp4">
					Your browser does not support the video tag.
				</video>
				<div style="position: absolute; top: 0; left: 0; width: 100%;  background-color: rgba(0, 0, 0, 0.5);"></div>
			</div>
				<div class="row" style="opacity: 0; transform: translateY(-50px); transition: opacity 0.5s ease, transform 0.5s ease; animation: fadeInUp 0.5s forwards 2s;">
					<div class="col-lg-9 offset-lg-2 text-center" style="position: relative; z-index: 1; margin:0 auto;">
						<div class="hero-text">
							<div class="hero-text-tablecell">
								<p class="subtitle" style="background-color: rgb(255 255 255 / 53%); padding: 10px; color:#080F0E; border-radius: 30px;">Elegant & Timeless</p>
								<h1>Exclusive Watches & Jewellery</h1>
								<div class="hero-btns">
									<a href="shop.php" class="boxed-btn">Shop Now</a>
									<a href="contact.php" class="bordered-btn">Contact Us</a>
								</div>
							</div>
						</div>
					</div>
				</div>
		</div>
	</div>
	<!-- end hero area -->

	<!-- features list section -->
	<div class="list-section pt-80 pb-80">
		<div class="container">

			<div class="row">
				<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
					<div class="list-box d-flex align-items-center">
						<div class="list-icon">
							<i class="fas fa-shipping-fast"></i>
						</div>
						<div class="content">
							<h3>Free Shipping</h3>
							<p>When order over $500</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 mb-4 mb-lg-0">
					<div class="list-box d-flex align-items-center">
						<div class="list-icon">
							<i class="fas fa-phone-volume"></i>
						</div>
						<div class="content">
							<h3>24/7 Support</h3>
							<p>Get support all day</p>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="list-box d-flex justify-content-start align-items-center">
						<div class="list-icon">
							<i class="fas fa-sync"></i>
						</div>
						<div class="content">
							<h3>Refund</h3>
							<p>Get refund within 3 days!</p>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- end features list section -->

	<!-- product section -->
	<div class="product-section mt-150 mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">	
						<h3><span class="orange-text">Our</span> Collection</h3>
						<p>Discover our exquisite range of watches and jewellery that blend style and elegance.</p>
					</div>
				</div>
			</div>

			<div class="row">
				<?php foreach ($latest_products as $product): ?>
					<div class="col-lg-4 col-md-6 text-center">
						<div class="single-product-item">
							<div class="product-image">
								<a href="single-product.php?id=<?= $product['product_id'] ?>"><img src="<?= $product['image_url'] ?>" alt="<?= htmlspecialchars($product['name']) ?>"></a>
							</div>
							<h3><?= htmlspecialchars($product['name']) ?></h3>
							<p class="product-price"><span>Price</span> $<?= number_format($product['price'], 2) ?></p>
							<form action="cart.php?action=add&id=<?= $product['product_id'] ?>" method="POST">
								<input type="hidden" name="quantity" value="1">
								<button type="submit" class="cart-btn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
							</form>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<!-- end product section -->

	<!-- cart banner section -->
	
    <!-- end cart banner section -->

	<!-- testimonail-section -->
	 
	<div class="testimonail-section mt-150 mb-150">
		<div class="container">
			
			<div class="row">
				
				<div class="col-lg-10 offset-lg-1 text-center">
				<div class="section-title">	
						<h3><span class="orange-text">Our</span> Team</h3>

					</div>
					<div class="owl-carousel team-carousel owl-theme">
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/konsto.png" alt="Konstantin">
							</div>
							<h3>konstontin grazonashvili</h3>
							<span>WEB DEVELOPER</span>
						</div>
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/oussa.png" alt="Oussama">
							</div>
							<h3>Oussama benyamin</h3>
							<span>WEB DEVELOPER</span>
						</div>
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/redha.png" alt="Redha">
							</div>
							<h3>Redha bourezgue</h3>
							<span>WEB DEVELOPER</span>
						</div>
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/malik.png" alt="Malik">
							</div>
							<h3>Malik</h3>
							<span>WEB DEVELOPER</span>
						</div>
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/abdela.jpg" alt="Abdellah">
							</div>
							<h3>Abdellah</h3>
							<span>WEB DEVELOPER</span>
						</div>
						<div class="team-member">
							<div class="member-img">
								<img src="assets/img_perso/moha.png" alt="Mohammed">
							</div>
							<h3>Mohammed</h3>
							<span>WEB DEVELOPER</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end testimonail-section -->
	
	<!-- advertisement section -->
	<div class="abt-section mb-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-12">
					<div>
						<img src="assets/img_perso/groupe.jpg" alt="Group 7 Team" class="about-image">
					</div>
				</div>
				<div class="col-lg-6 col-md-12">
					<div class="abt-text">
						<h2>We are <span class="orange-text">GRP 7</span></h2>
						<p>A team that was created in 2024 by NICOLA DEGABRIEL with the lead of konstontin</p>
						<p>with the help of Abd-Ellah and oussama , Malik, Redha and Mohammed, we were able to do a great job as a team, we send our thanks to NICOLA DEGABRIEL for wisely choosing us.</p>
						<a href="about.php" class="boxed-btn mt-4">know more</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- end advertisement section -->
	
	<!-- shop banner -->
	
	<!-- end shop banner -->

	
	<!-- end latest news -->

	<!-- logo carousel -->
	<div class="logo-carousel-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="logo-carousel-inner">
						<div class="single-logo-item">
							<img src="assets/img/company-logos/ap.png" alt="">
						</div>
						
						<div class="single-logo-item">
							<img src="assets/img/company-logos/omega.jpg" alt="">
						</div>
						<div class="single-logo-item">
							<img src="assets/img/company-logos/cartier.jpg" alt="">
						</div>
						<div class="single-logo-item">
							<img src="assets/img/company-logos/hublot.png" alt="">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
					


	<!-- end logo carousel -->

	<?php include 'includes/_footer.php' ?>
	
	



	<?php include 'includes/_register-login.php'; ?>

<div id="cookie-banner" class="cookie-banner">
  <div class="cookie-content">
    <p>Ce site utilise des cookies pour améliorer votre expérience. En continuant à naviguer sur ce site, vous acceptez notre utilisation des cookies.</p>
    <div class="cookie-buttons">
      <button id="accept-cookies" class="cookie-btn accept">Accepter</button>
      <button id="reject-cookies" class="cookie-btn reject">Rejeter</button>
      <a href="cookie-policy.php" class="cookie-link">En savoir plus</a>
    </div>
  </div>
</div>

<style>




.cookie-banner {
    display: none;
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #051922;
    color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    max-width: 400px;
    width: 90%;
    animation: slideUp 0.5s ease-out;
}

.cookie-content {
    text-align: center;
}

.cookie-content p {
    color: #fff;  /* Explicitly set text color to white */
    margin-bottom: 15px;
}

.cookie-buttons {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.cookie-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 50px;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.3s ease;
}

.cookie-btn.accept {
    background-color: #2F9985;
    color: #fff;
}

.cookie-btn.accept:hover {
    background-color: #fff;
    color: #2F9985;
}

.cookie-btn.reject {
    background-color: transparent;
    color: #fff;
    border: 2px solid #fff;
}

.cookie-btn.reject:hover {
    background-color: #fff;
    color: #051922;
}

.cookie-link {
    color: #2F9985;
    text-decoration: none;
    margin-left: 10px;
    font-weight: 600;
}

.cookie-link:hover {
    color: #fff;
    text-decoration: underline;
}

@keyframes slideUp {
    from {
        transform: translate(-50%, 100%);
        opacity: 0;
    }
    to {
        transform: translate(-50%, 0);
        opacity: 1;
    }
}

.testimonial-group {
    padding: 30px 0;
}

.owl-carousel .owl-item {
    transition: all 0.3s ease;
}

.owl-carousel .owl-item.center {
    transform: scale(1.1);
    z-index: 2;
}

.owl-carousel .owl-item:not(.center) {
    transform: scale(0.9);
    opacity: 0.8;
}

.owl-carousel .owl-stage-outer {
    padding: 30px 0;
}

.client-avater img {
    width: 100%;
    height: auto;
    border-radius: 10px;
    object-fit: cover;
}

.testimonial-sliders {
    padding: 40px 0;
    position: relative;
}

.team-carousel {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
}

.team-member {
    flex: 0 0 300px;
    text-align: center;
    transition: all 0.3s ease;
    padding: 20px;
}

.member-img {
    width: 100%;
    height: 300px;
    overflow: hidden;
    border-radius: 10px;
    margin-bottom: 15px;
}

.member-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-member h3 {
    margin: 10px 0 5px;
    font-size: 18px;
}

.team-member span {
    color: #2F9985;
    font-size: 14px;
}

/* Owl Carousel Specific Styles */
.owl-carousel .owl-stage {
    display: flex;
    align-items: center;
}

.owl-carousel .owl-item {
    transition: all 0.3s ease;
}

.owl-carousel .owl-item.active.center {
    transform: scale(1.15);
    z-index: 2;
}

.owl-carousel .owl-item:not(.center) {
    transform: scale(0.9);
}

/* Responsive Styles */
@media (max-width: 768px) {
    .team-carousel {
        gap: 15px;
    }
    
    .team-member {
        flex: 0 0 250px;
    }
    
    .member-img {
        height: 250px;
    }
    
    .owl-carousel .owl-item.active.center {
        transform: scale(1.03);
    }
}
</style>

<script>
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function showCookieBanner() {
    document.getElementById('cookie-banner').style.display = 'block';
}

function hideCookieBanner() {
    document.getElementById('cookie-banner').style.display = 'none';
}

function acceptCookies() {
    setCookie('cookie_consent', 'accepted', 365);
    hideCookieBanner();
    // Here you can enable your cookies and tracking scripts
}

function rejectCookies() {
    setCookie('cookie_consent', 'rejected', 365);
    hideCookieBanner();
    // Here you can disable non-essential cookies and tracking scripts
}

window.onload = function() {
    var consent = getCookie('cookie_consent');
    if (!consent) {
        showCookieBanner();
    }
}

document.getElementById('accept-cookies').addEventListener('click', acceptCookies);
document.getElementById('reject-cookies').addEventListener('click', rejectCookies);

$(document).ready(function(){
    if($('.team-carousel').length) {
        $('.team-carousel').owlCarousel({
            center: true,
            items: 3,
            loop: true,
            margin: 30,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            smartSpeed: 1000,
            responsive:{
                0:{
                    items: 1,
                    center: true,  // Enable center mode for mobile
                    margin: 0
                },
                576:{
                    items: 1,
                    center: true,  // Enable center mode for mobile
                    margin: 15
                },
                768:{
                    items: 3,
                    center: true,
                    margin: 30
                }
            }
        });
    }
});
</script>

