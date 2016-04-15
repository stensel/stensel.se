<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
if(!isset($_SESSION)) 
{ 
    session_start(); 
}
include "include/conn.php";

?><!DOCTYPE html>
<html lang="en">
	<head>
		<title>Johan Svendsrud EL AB / Stens EL</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-theme.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
	</head>
	
	<body class="body-custom">
		<div class="container-fluid hidden-xs">
			<div class="page-header page-header-custom">
					<div class="pull-left logo-txt">
						<h1>Johan Svendsrud EL AB / Stens EL</h1>
					</div>
					<div class="pull-right logo-img">
						<img src="images/logo_s.png" alt="Logo">
					</div>
					<div class="clearfix"></div>
			</div>
		</div>

		<nav class="navbar navbar-inverse hidden-print">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#home">Johan Svendsrud EL AB</a>
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav">
						<li class="active"><a href="#home" data-toggle="tab">Start</a></li>
						<li><a href="#map" data-toggle="tab">Karta</a></li>
						<li><a href="#contact" data-toggle="tab">Kontakt</a></li>
					</ul>
				</div>
			</div>
		</nav>

		<div class="container-fluid">
			<div class="tab-content">
				<div id="home" class="tab-pane fade in active tab-pane-custom">
					<h1>Välkommen till Johan Svendsrud EL AB / Stens El</h1>
						<p>
						Vi utf&ouml;r installationer och reparation av El, Tele, Data, Elektronik, och Larm
						f&ouml;r b&aring;de f&ouml;retag och privatpersoner.
						</p>
						<p>
						Har du funderingar eller vill ha en offert p&aring; ett arbete kontakta d&aring; oss.
						</p>
						<p>
						Johan Svendsrud EL AB / Stens El<br>
						&nbsp;&nbsp;Industrigatan 6C<br>
						&nbsp;&nbsp;673 32 Charlottenberg<br>
						&nbsp;&nbsp;Telefon: 0571-214 59<br>
						&nbsp;&nbsp;Fax: 0571-202 80<br>
						<br>
						&Ouml;ppettider<br>
						&nbsp;&nbsp;M&aring;ndag-Fredag 07:00 - 16:00<br>
						&nbsp;&nbsp;L&ouml;rdag, S&ouml;ndag - St&auml;ngt<br>
						</p>
				</div>
				<div id="map" class="tab-pane fade tab-pane-custom">
					<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2002.1906353451684!2d12.2959713!3d59.8791844!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4643a905ea756f27%3A0xa0e812d4a5838cb2!2sIndustrigatan+6C%2C+673+32+Charlottenberg!5e0!3m2!1ssv!2sse!4v1442555965834" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
				</div>
				<div id="contact" class="tab-pane fade tab-pane-custom">
<?php
    $result = DB::getInstance()->query("SELECT * FROM users WHERE Visible=1 ORDER BY FullName ASC");

    foreach($result as $row)
	{
		?>
					<div class="well kontakt_well">
						<img class="pull-left kontakt-img hidden-xs" src="images/user.png" alt="bild på användaren" />
						<div class="pull-left kontakt_txt">
							<span class="bold"><?php echo $row['FullName']?></span><br />
							<?php echo $row['ExtraInfo']?><br />
							Mobil: <?php echo $row['Phone']?><br />
							Epost: <a href="sendmail.php?toemail=<?php echo $row['EPost']?>"><?php echo $row['EPost']?></a>
						</div>
						<div class="clearfix"></div>
					</div>
<?php
	}
	
?>
				</div>
			</div>		
		</div>
	</body>
</html>

