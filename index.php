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
	<!-- Javascript -->
</head>
<body>
	<div class="container-fluid">
		<div class="row-fluid">
			
		</div>
		<div class="row-fluid">
			<div class="span2">
				<ul class="nav nav-pills nav-tabs nav-stacked">
					<li class="active"><a href="#" class="labelButton">Labels</a></li>
					<li><a href="#" class="shelfButton">Shelf Talkers</a></li>
					<li><a href="#" class="palletButton">Pallet Signs</a></li>
				</ul>	
			</div>
			<div class="span8">
				<?php require_once('components/labelTabs.php'); ?>
				<?php require_once('components/shelfTabs.php'); ?>
				<?php require_once('components/palletTabs.php'); ?>
			</div>
		</div>
		<div class="row-fluid">
			
		</div>
	</div>
</body>
</html>