<?php require("includes/library.inc.php"); ?>
<?php 
	$doc_root 	= "/duty-planner";
	$user		= "35SCE";
	$calendar 	= array(
		"ro" => $doc_root."/35SCE/public/basic.ics",
		"rw" => $doc_root."/35SCE/private-SxSaoqIjm9hG95wBeF9M/basic.ics"
    );
	$today		= new DateTime();
?>

<?php
	$base_color	= "blue-grey";
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Duty Planner</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Josefin+Sans&text=DUTYPLANNER" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Roboto+Mono|Material+Icons|Nunito:300" rel="stylesheet">

		<!-- Materialize CSS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/css/materialize.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0-rc.2/js/materialize.min.js"></script>
		<!-- /Materialize CSS -->

		<link rel="stylesheet" href="style.css" type="text/css">
		<script src="script.js"></script>
	</head>

	<body>
		<!-- NAVBAR -->
		<div class="navbar-fixed">
			<nav>
				<div class="nav-wrapper <?php echo $base_color?> darken-1">
					<a href="<?php echo $doc_root ?>/" class="brand-logo center">DUTY PLANNER</a>
					<a href="#" data-target="mobile-menu" class="sidenav-trigger"><i class="material-icons">menu</i></a>
					<ul id="nav-mobile" class="left hide-on-med-and-down">
						<li class="active"><a href="<?php echo $doc_root ?>/" class="waves-effect waves-light"><i class="material-icons">dashboard</i></a></li>
						<li><a href="<?php echo $doc_root ?>/settings.php" class="waves-effect waves-light"><i class="material-icons">settings</i></a></li>
						<li><a href="<?php echo $doc_root ?>/logout.php" class="waves-effect waves-light"><i class="material-icons">exit_to_app</i></a></li>
					</ul>
				</div>
			</nav>
		</div>
		
		<ul class="sidenav" id="mobile-menu">
			<li class="active"><a href="<?php echo $doc_root ?>/" class="waves-effect waves-light"><i class="material-icons">dashboard</i> Dashboard</a></li>
			<li><a href="<?php echo $doc_root ?>/settings.php" class="waves-effect waves-light"><i class="material-icons">settings</i> Settings</a></li>
			<li><a href="<?php echo $doc_root ?>/logout.php" class="waves-effect waves-light"><i class="material-icons">exit_to_app</i> Logout</a></li>
		</ul>
		<!-- /NAVBAR -->

		<div class="container" id="dashboard-wrap">
			<h3>Dashboard <small class="right">[<?php echo $user ?>]</small></h3>
			<div class="row">
				<div class="col s12 m6" id="download">
					<div class="card blue-grey darken-4">
						<div class="card-content white-text">
							<span class="card-title">Sync iCal</span>
							<p>You may use the links below for syncing with Google Calendar/Outlook etc.</p>
							<p class="red-text text-lighten-1">Please only share the public version with others. Sharing the private calendar will give read-write permissions to everyone.</p>
						</div>
						<div class="card-action">
							<a href="<?php echo $calendar["ro"] ?>" class="ical" data-pretext="<p class='green-text text-darken-2'>This calendar has read-only rights.</p>">Public</a>
							<a href="<?php echo $calendar["rw"] ?>" class="ical" data-pretext="<p class='red-text'>This calendar has read-write rights.</p>">Private</a>
						</div>
					</div>
				</div>
				
				<div class="col s12 m6" id="dutypersonnel">
					<div class="card <?php echo $base_color?> darken-3">
						<div class="card-content white-text">
							<span class="card-title">Duty Personnel</span>
							<p class="right <?php echo $base_color?>-text text-lighten-1"><i>Retrieved on <?php echo $today->format("Y-m-d H:i:s") ?> </i></p>
						</div>
						<div class="card-tabs">
							<ul class="tabs tabs-fixed-width tabs-transparent">
								<li class="tab"><a href="#duty-yesterday">Yesterday</a></li>
								<li class="tab"><a class="active" href="#duty-today">Today</a></li>
								<li class="tab"><a href="#duty-tomorrow">Tomorrow</a></li>
							</ul>
						</div>
						<div class="card-content grey lighten-3" id="dp-names">
							<!-- https://stackoverflow.com/a/44059132 -->
							<div id="duty-yesterday"><?php echo dutySummary($today->modify('-1 days')->format("Y-m-d")) ?></div>
							<div id="duty-today"><?php echo dutySummary($today->modify('+1 days')->format("Y-m-d")) ?></div>
							<div id="duty-tomorrow"><?php echo dutySummary($today->modify('+1 days')->format("Y-m-d")) ?></div>
							<?php $today -> modify('-1 days') /* Reset the date to original today */?>
						</div>
					</div>
				</div>
			</div>

		</div>

		<div id="modallen">
			<div id="alert" class="modal">
				<div class="modal-content">
					<h4 id="alert-header"></h4>
					<div id="alert-body"></div>
				</div>
				<div class="modal-footer">
					<a href="#" class="modal-close waves-effect waves-green btn-flat preventDefault">OK</a>
				</div>
			</div>
		</div>
	</body>
</html>