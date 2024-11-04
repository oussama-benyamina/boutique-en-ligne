<?php 
require 'functions/db_conn.php';
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'includes/_head-index.php'; ?>
<style>
	.team-bg img {
    width: 100%;
    height: 300px; /* Adjust this value as needed */
    object-fit: cover;
}

.social-link-team {
    padding: 0;
    list-style-type: none;
    display: flex;
    justify-content: center;
    margin-top: 15px;
}



.social-link-team a {
    color: #333;
    font-size: 20px;
    transition: color 0.3s ease;
}

.social-link-team a:hover {
    color: #f28123;
}
</style>

</head>
<body>
	
	
	
	<?php include 'includes/_header.php'; ?>

	

	<!-- team section -->
	<div class="mt-150">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 offset-lg-2 text-center">
					<div class="section-title">
						<h3>Our <span class="orange-text">Team</span></h3>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4 col-md-6">
					<div class="single-team-item">
						<div class="team-bg team-bg-1">
							<img src="assets/img_perso/REDHA.jpg" alt="" class="img-fluid">
						</div>
						<h4>Redha Bourezgue <span>WEB DESIGNER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
					
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="single-team-item">
						<div class="team-bg team-bg-1">
							<img src="assets/img_about_us/ouss_aboutus.jpg" alt="" class="img-fluid">
						</div>
						<h4>Benyamina oussama <span>WEB DEVELOPER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="single-team-item">
						<div class="team-bg team-bg-1">
							<img src="assets/img_about_us/malike_aboutus.jpg" alt="" class="img-fluid">
						</div>
						<h4>Malik <span>WEB DEVELOPER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
					
				</div>
				<div>
					
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="single-team-item">
						<div class="team-bg team-bg-1">
							<img src="assets/img_about_us/333.jpg" alt="" class="img-fluid">
						</div>
						<h4>Mohammed hammouche <span>WEB DEVELOPER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-md-6">
					<div class="single-team-item">
						<div class="team-bg team-bg-2">
							<img src="assets/img_about_us/kostontine_aboutus.jpg" alt="" class="img-fluid">
						</div>
						<h4>Konstontine Grazonashvili <span>WEB DEVELOPER & WEB DESIGNER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 offset-md-3 offset-lg-0">
					<div class="single-team-item">
						<div class="team-bg team-bg-3">
							<img src="assets/img_perso/abdela.jpg" alt="" class="img-fluid">
						</div>
						<h4>Abd-ellah </br>Hioun  <span>WEB DEVELOPER</span></h4>
						<ul class="social-link-team">
							<li><a href="#" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a></li>
							<li><a href="#" target="_blank" title="GitHub"><i class="fab fa-github"></i></a></li>
							<li><a href="#" target="_blank" title="Portfolio"><i class="fas fa-briefcase"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<!-- end team section -->

	<!-- testimonail-section -->
	
	<!-- end testimonail-section -->

	<!-- logo carousel -->
	
	

	<?php include 'includes/_register-login.php' ?>
	
	<?php include 'includes/_footer.php'?>

</body>
</html>
