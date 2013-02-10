<!DOCTYPE html>
<html>
<head>
	<title>Cost Plus 10 - Label Generator</title>
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="js/bootstrap.js"></script>
	<script src="js/placeholder.js"></script>
	<script src="js/nav.js"></script>
	<script src="js/label.js"></script>
	<script src="js/shelf.js"></script>
	<script src="js/pallet.js"></script>
	<script src="js/produce.js"></script>
	<!-- Javascript -->
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			<div id="logoHolder">
				<img class="logo" src="images/logo.png" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span2">
				<ul class="nav nav-pills nav-tabs nav-stacked">
					<li class="active"><a href="#" class="labelButton">Labels</a></li>
					<li><a href="#" class="shelfButton">Shelf Talkers</a></li>
					<li><a href="#" class="palletButton">Pallet Signs</a></li>
					<li><a href="#" class="produceButton">Produce Labels</a></li>
				</ul>	
			</div>
			<div class="span10">
				<?php require_once('components/labelTabs.php'); ?>
				<?php require_once('components/shelfTabs.php'); ?>
				<?php require_once('components/palletTabs.php'); ?>
				<?php require_once('components/produceTabs.php'); ?>
			</div>
		</div>
		<div class="row-fluid">
			
		</div>
	</div>
</body>
</html>